import {ChangeDetectorRef, Component, ElementRef, Input, OnInit, ViewChild} from '@angular/core';
import {MatCell, MatCellDef, MatColumnDef, MatHeaderCell, MatHeaderCellDef, MatHeaderRow, MatHeaderRowDef, MatRow, MatRowDef, MatTable, MatTableDataSource} from '@angular/material/table';
import {MatPaginator} from '@angular/material/paginator';
import {MatSort, Sort} from '@angular/material/sort';
import {MatDialog} from "@angular/material/dialog";
import {HttpService} from "../services/http.service";
import {MaterialEditComponentComponent} from "./material-edit-component/material-edit-component.component";
import {Artikel} from "../models/Artikel";
import {IDropdownSettings, NgMultiSelectDropDownModule} from "ng-multiselect-dropdown";
import {dateRangeValidator} from "../data-adapters/date.validator";
import {HttpParams} from "@angular/common/http";
import {AbstractControlOptions, FormBuilder, ReactiveFormsModule} from "@angular/forms";
import {LoginErrorComponent} from "../login/login-error/login-error.component";
import {BestellungEditComponentComponent} from "../data-grid-bestellungen/bestellung-edit-component/bestellung-edit-component.component";
import {MatToolbar} from "@angular/material/toolbar";
import {NgForOf, NgIf, NgStyle} from "@angular/common";
import {MatIcon} from "@angular/material/icon";
import {MatIconButton} from "@angular/material/button";
import {MatTooltip} from "@angular/material/tooltip";
import {FocusOnClickDirective} from "../shared/focus-on-click.directive";

@Component({
  selector: 'app-data-grid-artikel',
  templateUrl: './data-grid-artikel.component.html',
  styleUrls: ['./data-grid-artikel.component.css'],
  imports: [
    MatToolbar,
    NgIf,
    MatIcon,
    ReactiveFormsModule,
    NgMultiSelectDropDownModule,
    MatIconButton,
    MatTooltip,
    MatTable,
    MatSort,
    MatColumnDef,
    MatHeaderCell,
    MatCell,
    MatHeaderCellDef,
    MatCellDef,
    MatHeaderRow,
    MatRow,
    MatHeaderRowDef,
    MatRowDef,
    MatPaginator,
    NgForOf,
    FocusOnClickDirective,
    NgStyle
  ],
  animations: [
    // Your animation configurations here
  ]
})

export class DataGridArtikelComponent implements OnInit {
  @Input() departmentId: Number = 0;
  @ViewChild(MatSort) sort!: MatSort;
  @ViewChild(MatPaginator) paginator!: MatPaginator;
  displayedColumns: string[] = ['id', 'name', 'description', 'departments', 'lieferants', 'herstellers', 'add-bestellung', 'edit', 'remove'];
  dataSource = new MatTableDataSource<Artikel>([]);

  dropdownDepartmentSettings: IDropdownSettings = {};
  departments: any[] = [];
  originalDepartments: any[] = [];
  selectedDepartments: any[] = [];
  artikelGridForm: any;

  searchTerm: string = '';
  @ViewChild('searchInput') searchInput!: ElementRef;
  isSearchIconVisible: boolean = true;

  constructor(private httpService: HttpService, public dialog: MatDialog, private fb: FormBuilder, private cdr: ChangeDetectorRef) {
  }

  ngOnInit() {
    this.loadDepartments();
    this.dataSource.paginator = this.paginator;
    this.dataSource.sort = this.sort;

    this.dropdownDepartmentSettings = {
      idField: 'id',
      textField: 'name',
      allowSearchFilter: true,
      enableCheckAll: false,
      searchPlaceholderText: 'Suchen',
    };

    this.artikelGridForm = this.fb.group({
      departments: [[]],
    }, {validator: dateRangeValidator()} as AbstractControlOptions);
  }

  loadDepartments() {
    let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/department/get_departments');
    mitarbeiterRequest.subscribe((response: any) => {
      this.departments = response.data;
      this.originalDepartments = response.data;
      this.fetchDataByDepartmentId();
      this.cdr.detectChanges(); // Manually trigger change detection
    });
  }

  onDepartmentSelect(department: any) {
    if (department.name === 'Alle') {
      this.selectedDepartments = [department];
    } else {
      this.selectedDepartments = this.selectedDepartments.filter((d: any) => d.name !== 'Alle');
      let exists = this.selectedDepartments.some((d: any) => d.id === department.id);
      if (!exists) {
        this.selectedDepartments.push(department);
      } else {
        this.selectedDepartments = this.selectedDepartments.filter((d: any) => d.id !== department.id);
      }
    }

    this.artikelGridForm.get('departments')!.setValue(this.selectedDepartments);
    this.fetchDataByDepartmentId();
  }

  onFilterSelectChange() {
    this.fetchDataByDepartmentId();
  }

  getQueryParams() {
    let departments = this.artikelGridForm.get('departments').value;
    let params = new HttpParams();
    params = params.append('withAssociatedData', true);

    if (departments && departments.length > 0) {
      const hasIdZero = departments.some((dept: any) => dept.typ === 0);
      if (!hasIdZero) {
        params = params.append('departments', departments.map((dept: any) => dept.id).join(','));
      }
    }

    if (this.searchTerm) {
      params = params.append('search', this.searchTerm);
    }

    return params;
  }

  fetchDataByDepartmentId(): void {
    let params = this.getQueryParams();

    this.httpService.get_httpclient().get(`${this.httpService.get_baseUrl()}/artikel/0`, {params})
      .subscribe((response: any) => {
        this.dataSource = new MatTableDataSource<Artikel>(response.data);
        this.cdr.detectChanges(); // Manually trigger change detection
      });
  }

  sortData(sort: Sort) {
    const data = this.dataSource.data.slice();
    if (!sort.active || sort.direction === '') {
      this.dataSource.data = data;
      return;
    }

    this.dataSource.data = data.sort((a: Artikel, b: Artikel) => {
      const isAsc = sort.direction === 'asc';
      let key: string = sort.active.toString();

      return compare(`${a}.${key}`, `${b}.${key}`, isAsc);
    });
  }

  editRecord(record: Artikel) {
    this.loadArtikelFullData(record.id).then((data) => {
      data.formTitle = 'Artikel bearbeiten';

      const dialogRef = this.dialog.open(MaterialEditComponentComponent, {
        width: '550px',
        data: data,
        disableClose: true,
      });

      dialogRef.afterClosed().subscribe(result => {
        if (result && result.data) {
          // Update the data source with the edited record
          const index = this.dataSource.data.findIndex(artikel => artikel.id === result.data[0].id);
          const updatedData = [...this.dataSource.data];
          updatedData[index] = result.data[0];
          this.dataSource.data = updatedData;
          this.dataSource._updateChangeSubscription();
          this.cdr.detectChanges(); // Manually trigger change detection
        }
      });
    });
  }

  loadArtikelFullData(id: number): Promise<any> {
    return new Promise((resolve, reject) => {
      let url = this.httpService.get_baseUrl() + '/artikel/get_by_id/' + id;

      let artikelRequest = this.httpService.get_httpclient().get(url);
      artikelRequest.subscribe((response: any) => {
        if (response.success) {
          response.data[0].formTitle = 'Artikel bearbeiten';
          resolve(response.data[0]);
        } else {
          resolve({});
        }
      });
    });
  }

  addRecord() {
    const dialogRef = this.dialog.open(MaterialEditComponentComponent, {
      width: '550px',
      data: {},
      disableClose: true,
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        // Update the data source with the edited record
        const updatedData = [...this.dataSource.data];
        updatedData.unshift(result.data[0]);
        this.dataSource.data = updatedData;
        this.dataSource._updateChangeSubscription();
        this.cdr.detectChanges(); // Manually trigger change detection
      }
    });
  }

  addBestellung(record: Artikel) {
    let data: any = {};
    data.formTitle = 'Bestellung hinzufügen';
    data.artikels = [record];
    data.preis = record.preis;

    const dialogRef = this.dialog.open(BestellungEditComponentComponent, {
      width: '550px',
      maxHeight: '100vh',
      data,
      disableClose: true,
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        this.fetchDataByDepartmentId();
      }
    });
  }

  removeRecord(record: Artikel) {
    let url = this.httpService.get_baseUrl() + '/artikel/delete/' + record.id;

    this.httpService.get_httpclient().post(url, record.id).subscribe({
      next: (response: any) => {
        if (response && response.success && Boolean(response?.success)) {
          this.dataSource.data = this.dataSource.data.filter((value, key) => {
            return value.id !== record.id;
          });
          this.dataSource._updateChangeSubscription();
        } else {
          this.dialog.open(LoginErrorComponent, {
            width: '450px',
            height: '150px',
            data: {
              title: response?.message
            }
          });
        }
      },
      error: (err) => {
        console.log(err);
        this.dialog.open(LoginErrorComponent, {
          width: '450px',
          height: '150px',
          data: {
            title: 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut'
          }
        });
      }
    });
  }


  onArtikelSearchChange(searchWord: any) {
    let search: any = '';

    if (searchWord && searchWord?.target && searchWord.target.value) {
      search = searchWord.target.value;
    } else {
      search = searchWord;
    }

    if (search && search.length > 0) {
      this.searchTerm = search;
    } else {
      this.searchTerm = '';
    }

    this.fetchDataByDepartmentId();
  }

  setSearchIconVisible(isVisible: boolean) {
    this.isSearchIconVisible = isVisible;

    if (!isVisible) {
      setTimeout(() => this.searchInput?.nativeElement?.focus(), 0); // Focus input when it appears
    }
  }

  clearSearch() {
    this.searchTerm = '';
    this.isSearchIconVisible = true;
    this.fetchDataByDepartmentId();
  }

  preventBlur(event: Event) {
    event.preventDefault();
    event.stopPropagation();
  }
}

function compare(a: number | string, b: number | string, isAsc: boolean) {
  return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
