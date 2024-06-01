import {Component, OnInit, ViewChild} from '@angular/core';
import {MatTableDataSource} from '@angular/material/table';
import {MatPaginator} from '@angular/material/paginator';
import {MatSort, Sort} from '@angular/material/sort';
import {MatDialog} from "@angular/material/dialog";
import {MaterialEditComponentComponent} from "./material-edit-component/material-edit-component.component";

export interface UserData {
    id: number;
    name: string;
    quantity: number;
    description: string;
    manufacturer?: string;
}

@Component({
    selector: 'app-data-grid',
    templateUrl: './data-grid.component.html',
    styleUrls: ['./data-grid.component.css'],
    animations: [
        // Your animation configurations here
    ]
})

export class DataGridComponent implements OnInit {
    displayedColumns: string[] = ['id', 'name', 'quantity', 'description', 'manufacturer', 'edit', 'remove'];
    dataSource = new MatTableDataSource<UserData>([
        {id: 1, name: 'John Doe', quantity: 30, description: 'New York', manufacturer: 'Apple'},
        {id: 2, name: 'Jane Smith', quantity: 25, description: 'London', manufacturer: 'Samsung'},
        {id: 3, name: 'Sam Williams', quantity: 35, description: 'Sydney', manufacturer: 'Google'},
        {id: 4, name: 'Tom Brown', quantity: 40, description: 'Paris', manufacturer: 'Microsoft'},
        {id: 5, name: 'Kate Johnson', quantity: 45, description: 'Berlin', manufacturer: 'IBM'},
        {id: 6, name: 'Peter Davis', quantity: 50, description: 'Tokyo', manufacturer: 'Intel'},
        {id: 7, name: 'Tom Wilson', quantity: 55, description: 'Moscow', manufacturer: 'AMD'},
        {id: 8, name: 'John Miller', quantity: 60, description: 'Rome', manufacturer: 'Nvidia'},
        {id: 9, name: 'Jane Brown', quantity: 65, description: 'Madrid', manufacturer: 'Qualcomm'},
        {id: 10, name: 'Sam Smith', quantity: 70, description: 'Beijing', manufacturer: 'Huawei'},
        {id: 11, name: 'Kate Wilson', quantity: 75, description: 'Cairo', manufacturer: 'Sony'},
        {id: 12, name: 'Peter Johnson', quantity: 80, description: 'Istanbul', manufacturer: 'LG'},
        {id: 13, name: 'Tom Davis', quantity: 85, description: 'Rio de Janeiro', manufacturer: 'HTC'},
        {id: 14, name: 'John Wilson', quantity: 90, description: 'Cape Town', manufacturer: 'OnePlus'},
        {id: 15, name: 'Jane Miller', quantity: 95, description: 'Mumbai', manufacturer: 'Xiaomi'},
        {id: 16, name: 'Sam Brown', quantity: 100, description: 'Shanghai', manufacturer: 'Lenovo'},
        {id: 17, name: 'Kate Smith', quantity: 105, description: 'Toronto', manufacturer: 'Asus'},
        {id: 18, name: 'Peter Williams', quantity: 110, description: 'Los Angeles', manufacturer: 'Acer'},
        {id: 19, name: 'Tom Johnson', quantity: 115, description: 'Chicago', manufacturer: 'Dell'},
        {id: 20, name: 'John Smith', quantity: 120, description: 'Houston', manufacturer: 'HP'},
        {id: 21, name: 'Jane Doe', quantity: 125, description: 'Philadelphia', manufacturer: 'IBM'},
    ]);

    constructor(public dialog: MatDialog) {

    }

    @ViewChild(MatSort) sort!: MatSort;
    @ViewChild(MatPaginator) paginator!: MatPaginator;


    ngOnInit() {
        this.dataSource.paginator = this.paginator;
        this.dataSource.sort = this.sort;
    }

    sortData(sort: Sort) {
        const data = this.dataSource.data.slice();
        if (!sort.active || sort.direction === '') {
            this.dataSource.data = data;
            return;
        }

        this.dataSource.data = data.sort((a: UserData, b: UserData) => {
            const isAsc = sort.direction === 'asc';
            let key: string = sort.active.toString();

            return compare(`${a}.${key}`, `${b}.${key}`, isAsc);
        });
    }

    editRecord(record: UserData) {
        const dialogRef = this.dialog.open(MaterialEditComponentComponent, {
            width: '550px',
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
                result.id = this.dataSource.data.length + 1;
                this.dataSource.data.push(result);
                this.dataSource._updateChangeSubscription(); // Refresh the table
            }
        });
    }
    removeRecord(record: UserData) {
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
