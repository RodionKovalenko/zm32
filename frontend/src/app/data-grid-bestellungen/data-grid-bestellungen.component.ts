import { Component, OnInit, ViewChild, ChangeDetectorRef } from '@angular/core';
import { MatSort, Sort } from "@angular/material/sort";
import { MatPaginator } from "@angular/material/paginator";
import { MatTableDataSource } from "@angular/material/table";
import { MatDialog } from "@angular/material/dialog";
import { HttpService } from "../services/http.service";
import { BestellungEditComponentComponent } from "./bestellung-edit-component/bestellung-edit-component.component";
import { Bestellung } from "../models/Bestellung";
import { IDropdownSettings } from "ng-multiselect-dropdown";
import { FormBuilder, Validators } from "@angular/forms";


@Component({
    selector: 'app-data-grid-bestellungen',
    templateUrl: './data-grid-bestellungen.component.html',
    styleUrls: ['./data-grid-bestellungen.component.css']
})
export class DataGridBestellungenComponent implements OnInit {
    @ViewChild(MatSort) sort!: MatSort;
    @ViewChild(MatPaginator) paginator!: MatPaginator;
    displayedColumns: string[] = ['id', 'artikels', 'descriptionZusatz', 'lieferants', 'departments', 'herstellers', 'amount', 'preis', 'description', 'mitarbeiter', 'edit', 'remove'];
    dataSource = new MatTableDataSource<Bestellung>([]);

    dropdownDepartmentSettings: IDropdownSettings = {};
    departments: any[] = [];
    originalDepartments: any[] = [];
    selectedDepartments: any[] = [];
    bestellungForm: any;

    constructor(private httpService: HttpService, public dialog: MatDialog, private fb: FormBuilder, private cdr: ChangeDetectorRef) {}

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

        this.bestellungForm = this.fb.group({
            departments: [[], Validators.required],
        });
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

    fetchDataByDepartmentId(): void {
        let departmentId = this.selectedDepartments.map((d: any) => d.id);
        if (departmentId.length === 0) {
            departmentId = this.originalDepartments.map((d: any) => d.id);
        }

        let url = this.httpService.get_baseUrl() + '/bestellung/' + JSON.stringify(departmentId);
        let bestellungRequest = this.httpService.get_httpclient().get(url);

        bestellungRequest.subscribe((response: any) => {
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
                this.dataSource.data[index] = result;
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
                result.id = this.dataSource.data.length + 1;
                this.dataSource.data.push(result);
                this.dataSource._updateChangeSubscription();
                this.cdr.detectChanges(); // Manually trigger change detection
            }
        });
    }

    removeRecord(record: Bestellung) {
        const index = this.dataSource.data.findIndex(user => user.id === record.id);

        this.dataSource.data = this.dataSource.data.filter((value, key) => {
            return value.id !== record.id;
        });
        this.dataSource._updateChangeSubscription();
        this.cdr.detectChanges(); // Manually trigger change detection
    }
}

function compare(a: number | string, b: number | string, isAsc: boolean) {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
