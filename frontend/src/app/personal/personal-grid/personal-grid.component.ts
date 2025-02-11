import {ChangeDetectorRef, Component, ElementRef, OnInit, ViewChild} from '@angular/core';
import {MatSort, Sort} from "@angular/material/sort";
import {MatPaginator} from "@angular/material/paginator";
import {MatCell, MatCellDef, MatColumnDef, MatHeaderCell, MatHeaderCellDef, MatHeaderRow, MatHeaderRowDef, MatRow, MatRowDef, MatTable, MatTableDataSource} from "@angular/material/table";
import {HttpService} from "../../services/http.service";
import {MatDialog} from "@angular/material/dialog";
import {FormBuilder, ReactiveFormsModule} from "@angular/forms";
import {HttpParams} from "@angular/common/http";
import {Artikel} from "../../models/Artikel";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {Mitarbeiter} from "../../models/Mitarbeiter";
import {IDropdownSettings, NgMultiSelectDropDownModule} from "ng-multiselect-dropdown";
import {PersonalFormComponent} from "../personal-form/personal-form.component";
import {MatToolbar} from "@angular/material/toolbar";
import {MatIcon} from "@angular/material/icon";
import {MatIconButton} from "@angular/material/button";
import {NgIf, NgStyle} from "@angular/common";
import {MatTooltip} from "@angular/material/tooltip";

@Component({
  selector: 'app-personal-grid',
  templateUrl: './personal-grid.component.html',
  imports: [
    MatToolbar,
    MatIcon,
    ReactiveFormsModule,
    NgMultiSelectDropDownModule,
    MatIconButton,
    MatTable,
    MatSort,
    MatColumnDef,
    MatHeaderCell,
    MatCell,
    MatHeaderRow,
    MatRow,
    MatPaginator,
    NgIf,
    MatTooltip,
    MatHeaderRowDef,
    MatRowDef,
    MatHeaderCellDef,
    MatCellDef,
    NgStyle
  ],
  styleUrl: './personal-grid.component.css'
})
export class PersonalGridComponent implements OnInit {
  @ViewChild(MatSort) sort!: MatSort;
  @ViewChild(MatPaginator) paginator!: MatPaginator;
  displayedColumns: string[] = ['id', 'firstname', 'lastname', 'mitarbeiterId', 'edit', 'remove'];
  dataSource = new MatTableDataSource<Mitarbeiter>([]);

  dropdownDepartmentSettings: IDropdownSettings = {};

  departments: any[] = [];
  originalDepartments: any[] = [];
  selectedDepartments: any[] = [];
  personalGridForm: any;

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

    this.personalGridForm = this.fb.group({
      departments: [[]],
    });
  }

  loadDepartments() {
    let mitarbeiterRequest = this.httpService.get_httpclient().get(`${this.httpService.get_baseUrl()}/department/get_departments`);
    mitarbeiterRequest.subscribe((response: any) => {
      this.departments = response.data;
      this.originalDepartments = response.data;
      this.cdr.detectChanges();

      this.loadPersonal();
    });
  }

  loadPersonal() {
    let params = this.getQueryParams();

    let mitarbeiterRequest = this.httpService.get_httpclient().get(
      `${this.httpService.get_baseUrl()}/user/get_user`, {params}
    );
    mitarbeiterRequest.subscribe((response: any) => {
      this.dataSource = new MatTableDataSource<Mitarbeiter>(response.data);
      this.cdr.detectChanges();
    });
  }

  getQueryParams() {
    let params = new HttpParams();

    let departmentId = this.personalGridForm.get('departments')?.value.map((d: any) => d.id);

    if (departmentId.length === 0) {
      departmentId = this.originalDepartments.map((d: any) => d.id);
    }
    params = params.append('departments', departmentId.join(','));

    if (this.searchTerm) {
      params = params.append('search', this.searchTerm);
    }

    return params;
  }

  sortData(sort: Sort) {
    const data = this.dataSource.data.slice();
    if (!sort.active || sort.direction === '') {
      this.dataSource.data = data;
      return;
    }

    this.dataSource.data = data.sort((a: Mitarbeiter, b: Mitarbeiter) => {
      const isAsc = sort.direction === 'asc';
      let key: string = sort.active.toString();

      return compare(`${a}.${key}`, `${b}.${key}`, isAsc);
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

    this.personalGridForm.get('departments')!.setValue(this.selectedDepartments);
    this.loadPersonal();
  }

  editRecord(record: Artikel) {
    record.formTitle = 'Benutzer bearbeiten';

    const dialogRef = this.dialog.open(PersonalFormComponent, {
      width: '550px',
      data: record,
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
  }

  addRecord() {
    let data: any = {};
    data['formTitle'] = 'Einen neuen Benutzer hinzufügen';

    const dialogRef = this.dialog.open(PersonalFormComponent, {
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

  removeRecord(record: Artikel) {
    let url = this.httpService.get_baseUrl() + '/user/delete/' + record.id;

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

  onPersonalSearchChange(searchWord: any) {
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

    this.loadPersonal();
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
    this.loadPersonal();
  }

  preventBlur(event: Event) {
    event.preventDefault();
    event.stopPropagation();
  }
}

function compare(a: number | string, b: number | string, isAsc: boolean) {
  return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
