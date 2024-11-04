import {ChangeDetectorRef, Component, OnInit, ViewChild} from '@angular/core';
import {MatSort, Sort} from "@angular/material/sort";
import {MatPaginator} from "@angular/material/paginator";
import {MatTableDataSource} from "@angular/material/table";
import {Artikel} from "../../models/Artikel";
import {HttpService} from "../../services/http.service";
import {MatDialog} from "@angular/material/dialog";
import {FormBuilder} from "@angular/forms";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {HttpParams} from "@angular/common/http";
import {DepartmentFormComponent} from "../department-form/department-form.component";
import {DepartmentData} from "../../models/Department";

@Component({
    selector: 'app-department-grid',
    templateUrl: './department-grid.component.html',
    styleUrl: './department-grid.component.css'
})

export class DepartmentGridComponent implements OnInit {
    @ViewChild(MatSort) sort!: MatSort;
    @ViewChild(MatPaginator) paginator!: MatPaginator;
    displayedColumns: string[] = ['id', 'name', 'edit', 'remove'];
    dataSource = new MatTableDataSource<DepartmentData>([]);
    searchTerm: string = '';

    constructor(private httpService: HttpService, public dialog: MatDialog, private fb: FormBuilder, private cdr: ChangeDetectorRef) {
    }

    ngOnInit() {
        this.loadDepartments();
        this.dataSource.paginator = this.paginator;
        this.dataSource.sort = this.sort;
    }

    loadDepartments() {
        let params = new HttpParams();

        if (this.searchTerm) {
            params = params.append('search', this.searchTerm);
        }

        let mitarbeiterRequest = this.httpService.get_httpclient().get(
            `${this.httpService.get_baseUrl()}/department/get_departments`, {params}
        );
        mitarbeiterRequest.subscribe((response: any) => {
            this.dataSource = new MatTableDataSource<DepartmentData>(response.data);
            this.cdr.detectChanges(); // Manually trigger change detection
        });
    }

    sortData(sort: Sort) {
        const data = this.dataSource.data.slice();
        if (!sort.active || sort.direction === '') {
            this.dataSource.data = data;
            return;
        }

        this.dataSource.data = data.sort((a: DepartmentData, b: DepartmentData) => {
            const isAsc = sort.direction === 'asc';
            let key: string = sort.active.toString();

            return compare(`${a}.${key}`, `${b}.${key}`, isAsc);
        });
    }

    editRecord(record: Artikel) {
        record.formTitle = 'Abteilung bearbeiten';

        const dialogRef = this.dialog.open(DepartmentFormComponent, {
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
        data['formTitle'] = 'Neue Abteilung anlegen';

        const dialogRef = this.dialog.open(DepartmentFormComponent, {
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

    removeRecord(record: Artikel) {
        let url = this.httpService.get_baseUrl() + '/department/delete/' + record.id;

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

        this.loadDepartments();
    }

    clearSearch() {
        this.searchTerm = '';
        this.loadDepartments();
    }
}

function compare(a: number | string, b: number | string, isAsc: boolean) {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}