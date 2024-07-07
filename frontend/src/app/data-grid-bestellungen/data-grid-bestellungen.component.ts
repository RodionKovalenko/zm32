import {Component, Input, OnChanges, OnInit, SimpleChanges, ViewChild} from '@angular/core';
import {MatSort, Sort} from "@angular/material/sort";
import {MatPaginator} from "@angular/material/paginator";
import {MatTableDataSource} from "@angular/material/table";
import {MaterialData} from "../models/Material";
import {MatDialog} from "@angular/material/dialog";
import {HttpService} from "../services/http.service";
import {BestellungEditComponentComponent} from "./bestellung-edit-component/bestellung-edit-component.component";

@Component({
  selector: 'app-data-grid-bestellungen',
  templateUrl: './data-grid-bestellungen.component.html',
  styleUrl: './data-grid-bestellungen.component.css'
})
export class DataGridBestellungenComponent implements OnInit, OnChanges {
  @Input() departmentId: Number = 0;
  @ViewChild(MatSort) sort!: MatSort;
  @ViewChild(MatPaginator) paginator!: MatPaginator;
  displayedColumns: string[] = ['id', 'name', 'description', 'quantity', 'edit', 'remove'];
  dataSource = new MatTableDataSource<MaterialData>([]);

  constructor(private httpService: HttpService, public dialog: MatDialog) {
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['departmentId']) {
      this.fetchDataByDepartmentId(this.departmentId);
    }
  }

  fetchDataByDepartmentId(departmentId: Number): void {
    let url = this.httpService.get_baseUrl() + '/bestellung/' + departmentId;
    let mitarbeiterRequest = this.httpService.get_httpclient().get(url);

    mitarbeiterRequest.subscribe((response: any) => {
      this.dataSource = new MatTableDataSource<MaterialData>(response.data);
    });
  }
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

    this.dataSource.data = data.sort((a: MaterialData, b: MaterialData) => {
      const isAsc = sort.direction === 'asc';
      let key: string = sort.active.toString();

      return compare(`${a}.${key}`, `${b}.${key}`, isAsc);
    });
  }

  editRecord(record: MaterialData) {
    const dialogRef = this.dialog.open(BestellungEditComponentComponent, {
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
    const dialogRef = this.dialog.open(BestellungEditComponentComponent, {
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
  removeRecord(record: MaterialData) {
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
