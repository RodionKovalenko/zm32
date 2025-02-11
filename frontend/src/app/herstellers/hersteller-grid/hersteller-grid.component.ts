import {ChangeDetectorRef, Component, ElementRef, Input, OnInit, ViewChild} from '@angular/core';
import {MatSort, Sort} from "@angular/material/sort";
import {MatPaginator} from "@angular/material/paginator";
import {MatCell, MatCellDef, MatColumnDef, MatHeaderCell, MatHeaderCellDef, MatHeaderRow, MatHeaderRowDef, MatRow, MatRowDef, MatTable, MatTableDataSource} from "@angular/material/table";
import {IDropdownSettings} from "ng-multiselect-dropdown";
import {HttpService} from "../../services/http.service";
import {MatDialog} from "@angular/material/dialog";
import {AbstractControlOptions, FormBuilder} from "@angular/forms";
import {dateRangeValidator} from "../../data-adapters/date.validator";
import {HttpParams} from "@angular/common/http";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {Hersteller} from "../../models/Hersteller";
import {HerstellerEditComponentComponent} from "../../data-grid-artikel/hersteller-edit-component/hersteller-edit-component.component";
import {MatToolbar} from "@angular/material/toolbar";
import {NgForOf, NgIf, NgStyle} from "@angular/common";
import {MatFormFieldModule} from "@angular/material/form-field";
import {MatInputModule} from "@angular/material/input";
import {MatIcon} from "@angular/material/icon";
import {MatTooltip} from "@angular/material/tooltip";
import {MatIconButton} from "@angular/material/button";

@Component({
  selector: 'app-hersteller-grid',
  templateUrl: './hersteller-grid.component.html',
  imports: [
    MatToolbar,
    NgIf,
    MatFormFieldModule,
    MatInputModule,
    MatIcon,
    MatTooltip,
    MatIconButton,
    MatTable,
    MatSort,
    MatHeaderCellDef,
    MatHeaderCell,
    MatCell,
    MatCellDef,
    MatColumnDef,
    NgForOf,
    MatHeaderRow,
    MatRow,
    MatHeaderRowDef,
    MatRowDef,
    MatPaginator,
    NgStyle,
  ],
  styleUrl: './hersteller-grid.component.css'
})
export class HerstellerGridComponent implements OnInit {
  @Input() departmentId: Number = 0;
  @ViewChild(MatSort) sort!: MatSort;
  @ViewChild(MatPaginator) paginator!: MatPaginator;
  displayedColumns: string[] = ['id', 'name', 'standorte', 'edit', 'remove'];
  dataSource = new MatTableDataSource<Hersteller>([]);

  dropdownDepartmentSettings: IDropdownSettings = {};
  herstellerGridForm: any;

  searchTerm: string = '';
  @ViewChild('searchInput') searchInput!: ElementRef;
  isSearchIconVisible: boolean = true;

  constructor(private httpService: HttpService, public dialog: MatDialog, private fb: FormBuilder, private cdr: ChangeDetectorRef) {
  }

  ngOnInit() {
    this.fetchHerstellerByParams();
    this.dataSource.paginator = this.paginator;
    this.dataSource.sort = this.sort;

    this.dropdownDepartmentSettings = {
      idField: 'id',
      textField: 'name',
      allowSearchFilter: true,
      enableCheckAll: false,
      searchPlaceholderText: 'Suchen',
    };

    this.herstellerGridForm = this.fb.group({}, {validator: dateRangeValidator()} as AbstractControlOptions);
  }

  getQueryParams() {
    let params = new HttpParams();

    if (this.searchTerm) {
      params = params.append('search', this.searchTerm);
    }

    return params;
  }

  fetchHerstellerByParams(): void {
    let params = this.getQueryParams();

    this.httpService.get_httpclient().get(`${this.httpService.get_baseUrl()}/hersteller/get_hersteller`, {params})
      .subscribe((response: any) => {
        this.dataSource = new MatTableDataSource<Hersteller>(response.data);
        this.cdr.detectChanges(); // Manually trigger change detection
      });
  }

  sortData(sort: Sort) {
    const data = this.dataSource.data.slice();
    if (!sort.active || sort.direction === '') {
      this.dataSource.data = data;
      return;
    }

    this.dataSource.data = data.sort((a: Hersteller, b: Hersteller) => {
      const isAsc = sort.direction === 'asc';
      let key: string = sort.active.toString();

      return compare(`${a}.${key}`, `${b}.${key}`, isAsc);
    });
  }

  editRecord(record: Hersteller) {
    record.formTitle = 'Hersteller bearbeiten';

    const dialogRef = this.dialog.open(HerstellerEditComponentComponent, {
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

    data.formTitle = 'Hersteller hinzufügen';
    const dialogRef = this.dialog.open(HerstellerEditComponentComponent, {
      width: '550px',
      data: data,
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

  removeRecord(record: Hersteller) {
    let url = this.httpService.get_baseUrl() + '/hersteller/delete/' + record.id;

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

  onHerstellerSearchChange(searchWord: any) {
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

    this.fetchHerstellerByParams();
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
    this.fetchHerstellerByParams();
  }

  preventBlur(event: Event) {
    event.preventDefault();
    event.stopPropagation();
  }
}

function compare(a: number | string, b: number | string, isAsc: boolean) {
  return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
