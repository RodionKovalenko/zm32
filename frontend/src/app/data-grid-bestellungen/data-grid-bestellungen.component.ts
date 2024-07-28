import { Component, OnChanges, OnInit, SimpleChanges, ViewChild } from '@angular/core';
import { MatSort, Sort } from "@angular/material/sort";
import { MatPaginator } from "@angular/material/paginator";
import { MatTableDataSource } from "@angular/material/table";
import { MatDialog } from "@angular/material/dialog";
import { HttpService } from "../services/http.service";
import { BestellungEditComponentComponent } from "./bestellung-edit-component/bestellung-edit-component.component";
import { Bestellung } from "../models/Bestellung";
import { DepartmentData } from "../models/Department";

@Component({
    selector: 'app-data-grid-bestellungen',
    templateUrl: './data-grid-bestellungen.component.html',
    styleUrl: './data-grid-bestellungen.component.css'
})
export class DataGridBestellungenComponent implements OnInit, OnChanges {
    @ViewChild(MatSort) sort!: MatSort;
    @ViewChild(MatPaginator) paginator!: MatPaginator;
    displayedColumns: string[] = ['id', 'artikels', 'descriptionZusatz', 'lieferants', 'departments', 'herstellers', 'amount', 'preis', 'description', 'mitarbeiter', 'edit', 'remove'];
    dataSource = new MatTableDataSource<Bestellung>([]);

    departmentRecords: DepartmentData[] = [{ id: 0, name: 'test', typ: 0 }];
    selectedDepartment: DepartmentData = { id: 0, name: '', typ: 0 };

    constructor(private httpService: HttpService, public dialog: MatDialog) { }

    ngOnInit() {
        this.loadDepartments();
        this.dataSource.paginator = this.paginator;
        this.dataSource.sort = this.sort;
    }

    loadDepartments() {
        let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/department/get_departments');
        mitarbeiterRequest.subscribe((response: any) => {
            this.departmentRecords = response.data;
            this.selectedDepartment = this.departmentRecords[0];
            this.fetchDataByDepartmentId(this.selectedDepartment.id);
        });
    }

    onDepartmentChange(newValue: DepartmentData): void {
        this.selectedDepartment = newValue;
        this.fetchDataByDepartmentId(this.selectedDepartment.id);
    }

    ngOnChanges(changes: SimpleChanges): void {
        if (changes['departmentId']) {
            this.fetchDataByDepartmentId(this.selectedDepartment.id);
        }
    }

    fetchDataByDepartmentId(departmentId: Number): void {
        let url = this.httpService.get_baseUrl() + '/bestellung/' + departmentId;
        let bestellungRequest = this.httpService.get_httpclient().get(url);

        bestellungRequest.subscribe((response: any) => {
            this.dataSource = new MatTableDataSource<Bestellung>(response.data);
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
        record.departmentId = this.selectedDepartment.id
        record.formTitle = 'Bestellung bearbeiten';

        const dialogRef = this.dialog.open(BestellungEditComponentComponent, {
            width: '550px',
            maxHeight: '100vh',
            data: record,
            disableClose: true,
        });

        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                // Update the data source with the edited record
                const index = this.dataSource.data.findIndex(user => user.id === result.id);
                this.dataSource.data[index] = result;
                this.dataSource._updateChangeSubscription(); // Refresh the table
            }
        });

        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                // Here, you should call your service method to fetch updated data
                this.fetchDataByDepartmentId(this.selectedDepartment.id); // Example method to reload data
            }
        });
    }

    addRecord() {
        let data: any = {};

        data.formTitle = 'Bestellung hinzufügen';

        const dialogRef = this.dialog.open(BestellungEditComponentComponent, {
            width: '550px',
            // height: '100vh',
            maxHeight: '100vh',
            data,
            disableClose: true,
        });

        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                // Update the data source with the edited record
                result.id = this.dataSource.data.length + 1;
                this.dataSource.data.push(result);
                this.dataSource._updateChangeSubscription();
            }
        });

        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.fetchDataByDepartmentId(this.selectedDepartment.id);
            }
        });
    }

    removeRecord(record: Bestellung) {
        const index = this.dataSource.data.findIndex(user => user.id === record.id);

        this.dataSource.data = this.dataSource.data.filter((value, key) => {
            return value.id !== record.id;
        });
        this.dataSource._updateChangeSubscription();
    }
}

function compare(a: number | string, b: number | string, isAsc: boolean) {
    return (a < b ? -1 : 1) * (isAsc ? 1 : -1);
}
