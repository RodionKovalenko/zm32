import {Component, OnInit, ViewChild, ChangeDetectorRef, ElementRef} from '@angular/core';
import {MatSort, Sort} from "@angular/material/sort";
import {MatPaginator} from "@angular/material/paginator";
import {MatCell, MatCellDef, MatColumnDef, MatHeaderCell, MatHeaderCellDef, MatHeaderRow, MatHeaderRowDef, MatRow, MatRowDef, MatTable, MatTableDataSource} from "@angular/material/table";
import {MatDialog} from "@angular/material/dialog";
import {HttpService} from "../services/http.service";
import {BestellungEditComponentComponent} from "./bestellung-edit-component/bestellung-edit-component.component";
import {Bestellung} from "../models/Bestellung";
import {IDropdownSettings, NgMultiSelectDropDownModule} from "ng-multiselect-dropdown";
import {AbstractControlOptions, FormBuilder, FormsModule, ReactiveFormsModule, Validators} from "@angular/forms";
import {LoginErrorComponent} from "../login/login-error/login-error.component";
import {HttpParams} from "@angular/common/http";
import {dateRangeValidator} from "../data-adapters/date.validator";
import {MatToolbar} from "@angular/material/toolbar";
import {MatIcon} from "@angular/material/icon";
import {MatFormFieldModule} from "@angular/material/form-field";
import {MatInputModule} from "@angular/material/input";
import {DatePipe, NgForOf, NgIf, NgStyle} from "@angular/common";
import {MatIconButton} from "@angular/material/button";
import {MatTooltip} from "@angular/material/tooltip";
import {MatDatepicker, MatDatepickerInput, MatDatepickerToggle} from "@angular/material/datepicker";
import {MatOption, MatSelect} from "@angular/material/select";
import {FocusOnClickDirective} from "../shared/focus-on-click.directive";

@Component({
  selector: 'app-data-grid-bestellungen',
  templateUrl: './data-grid-bestellungen.component.html',
  standalone: true,
  imports: [
    MatToolbar,
    MatIcon,
    MatFormFieldModule,
    MatInputModule,
    NgIf,
    MatIconButton,
    MatTooltip,
    ReactiveFormsModule,
    NgMultiSelectDropDownModule,
    MatDatepickerInput,
    MatDatepickerToggle,
    MatDatepicker,
    MatTable,
    MatSort,
    MatColumnDef,
    MatHeaderCell,
    MatCell,
    NgForOf,
    MatSelect,
    MatOption,
    FormsModule,
    MatHeaderRow,
    MatRow,
    MatPaginator,
    MatHeaderRowDef,
    MatRowDef,
    MatHeaderCellDef,
    MatCellDef,
    FocusOnClickDirective,
    DatePipe,
    NgStyle
  ],
  styleUrls: ['./data-grid-bestellungen.component.css']
})
export class DataGridBestellungenComponent implements OnInit {
  @ViewChild(MatSort) sort!: MatSort;
  @ViewChild(MatPaginator) paginator!: MatPaginator;
  displayedColumns: string[] = ['id', 'artikels', 'descriptionZusatz', 'lieferants', 'departments', 'herstellers',
    'amount', 'preis', 'description', 'datum', 'status', 'mitarbeiter', 'edit'];
  dataSource = new MatTableDataSource<Bestellung>([]);

  dropdownDepartmentSettings: IDropdownSettings = {};
  departments: any[] = [];
  originalDepartments: any[] = [];
  selectedDepartments: any[] = [];
  bestellungForm: any;

  searchTerm: string = '';
  @ViewChild('searchInput') searchInput!: ElementRef;
  isSearchIconVisible: boolean = true;

  statusOptions = [
    {id: 1, name: 'Offen'},
    {id: 2, name: 'Bestellt'},
    {id: 3, name: 'Geliefert'},
    {id: 4, name: 'Storniert'},
  ];

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

    let currentDate = new Date();

    let dateSinceAgo = new Date();

    currentDate.setHours(0, 0, 0, 0);
    dateSinceAgo.setDate(currentDate.getDate() - 60);

    this.bestellungForm = this.fb.group({
      departments: [[]],
      status: [[this.statusOptions[0]]],
      datum: [dateSinceAgo],
      datumBis: ['']
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

    this.bestellungForm.get('departments')!.setValue(this.selectedDepartments);
    this.fetchDataByDepartmentId();
  }

  onFilterSelectChange() {
    this.fetchDataByDepartmentId();
  }

  getQueryParams() {
    let departmentId = this.bestellungForm.get('departments')?.value.map((d: any) => d.id);
    let status = this.bestellungForm.get('status')?.value.map((d: any) => d.id);
    let datum = this.bestellungForm.get('datum').value;
    let datumBis = this.bestellungForm.get('datumBis').value;

    if (departmentId.length === 0) {
      departmentId = this.originalDepartments.map((d: any) => d.id);
    }

    let params = new HttpParams();
    params = params.append('departments', JSON.stringify(departmentId));

    if (status.length !== 0) {
      params = params.append('status', JSON.stringify(status));
    }
    if (datum instanceof Date) {
      datum.setHours(0, 0, 0, 0);
      params = params.append('createdAfter', datum.toISOString());
    }
    if (datumBis instanceof Date) {
      datumBis.setHours(23, 59, 59, 999);
      params = params.append('datumBis', datumBis.toISOString());
    }
    if (this.searchTerm) {
      params = params.append('search', this.searchTerm);
    }

    return params;
  }

  fetchDataByDepartmentId(): void {
    let params = this.getQueryParams();

    this.httpService.get_httpclient().get(`${this.httpService.get_baseUrl()}/bestellung/get_bestellungen`, {params})
      .subscribe((response: any) => {
        this.dataSource = new MatTableDataSource<Bestellung>(response.data);
        this.cdr.detectChanges(); // Manually trigger change detection
      });
  }

  sortData(sort: Sort) {
    const data = this.dataSource.data.slice();
    if (!sort.active || sort.direction === '') {
      this.dataSource.data = data;
      return;
    }

    this.dataSource.data = data.sort((a: Bestellung, b: Bestellung) => {
      const isAsc = sort.direction === 'asc';
      let key: string = sort.active.toString();

      return compare(`${a}.${key}`, `${b}.${key}`, isAsc);
    });
  }

  editRecord(record: any) {
    record.formTitle = 'Bestellung bearbeiten';

    const dialogRef = this.dialog.open(BestellungEditComponentComponent, {
      width: '550px',
      maxHeight: '100vh',
      data: record,
      disableClose: true,
    });

    dialogRef.afterClosed().subscribe(result => {
      if (result) {
        const index = this.dataSource.data.findIndex(user => user.id === result.id);
        const updatedData = [...this.dataSource.data];
        updatedData[index] = result;
        this.dataSource.data = updatedData;
        this.dataSource._updateChangeSubscription();
        this.cdr.detectChanges(); // Manually trigger change detection
      }
    });
  }

  addRecord() {
    let data: any = {};
    data.formTitle = 'Bestellung hinzufügen';

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

  removeRecord(record: Bestellung) {
    let url = this.httpService.get_baseUrl() + '/bestellung/delete/' + record.id;

    this.httpService.get_httpclient().post(url, record.id).subscribe({
      next: (response: any) => {
        if (response && response.success && Boolean(response?.success)) {
          this.fetchDataByDepartmentId();
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

  getArtikelDescription(element: any) {
    if (element && element.artikels && element.artikels.length > 0) {
      return element.artikels[0]?.description;
    }
    return '';
  }

  onBestellungStatusChange(element: any) {
    let url = this.httpService.get_baseUrl() + '/bestellung/update_status/' + element.id;
    let formValue = {
      status: element.status
    };

    this.httpService.get_httpclient().post(url, formValue).subscribe({
      next: (response: any) => {
        if (response && response.success && Boolean(response?.success)) {
          this.fetchDataByDepartmentId();
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

  exportData() {
    let params = this.getQueryParams();

    this.httpService.get_httpclient()
      .get(`${this.httpService.get_baseUrl()}/bestellung/download_bestellungen`, {
        responseType: 'blob',
        params: params
      })
      .subscribe({
        next: (response: any) => {
          const url = window.URL.createObjectURL(response);
          const a = document.createElement('a');
          a.href = url;
          let date = new Date();
          let dateString = date.getDate() + '-' + (date.getMonth() + 1) + '-' + date.getFullYear();

          a.download = 'zm32_bestellungen' + dateString + '.pdf';
          a.click();
          window.URL.revokeObjectURL(url); // Clean up the URL object
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


  onBestellungSearchChange(searchWord: any) {
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
