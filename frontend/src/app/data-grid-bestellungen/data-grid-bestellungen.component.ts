import {Component, Input, OnChanges, OnInit, SimpleChanges, ViewChild} from '@angular/core';
import {MatSort, Sort} from "@angular/material/sort";
import {MatPaginator} from "@angular/material/paginator";
import {MatTableDataSource} from "@angular/material/table";
import {MatDialog} from "@angular/material/dialog";
import {HttpService} from "../services/http.service";
import {BestellungEditComponentComponent} from "./bestellung-edit-component/bestellung-edit-component.component";
import {Bestellung} from "../models/Bestellung";

@Component({
  selector: 'app-data-grid-bestellungen',
  templateUrl: './data-grid-bestellungen.component.html',
  styleUrl: './data-grid-bestellungen.component.css'
})
export class DataGridBestellungenComponent implements OnInit, OnChanges {
  @Input() departmentId: Number = 0;
  @ViewChild(MatSort) sort!: MatSort;
  @ViewChild(MatPaginator) paginator!: MatPaginator;
  displayedColumns: string[] = ['id', 'artikel', 'lieferant', 'descriptionZusatz', 'amount', 'preis', 'description', 'mitarbeiter', 'edit', 'remove'];
  dataSource = new MatTableDataSource<Bestellung>([]);

  constructor(private httpService: HttpService, public dialog: MatDialog) {
  }

  ngOnChanges(changes: SimpleChanges): void {
    if (changes['departmentId']) {
      this.fetchDataByDepartmentId(this.departmentId);
    }
  }

  fetchDataByDepartmentId(departmentId: Number): void {
    let url = this.httpService.get_baseUrl() + '/bestellung/' + departmentId;
    let bestellungRequest = this.httpService.get_httpclient().get(url);

    bestellungRequest.subscribe((response: any) => {
      this.dataSource = new MatTableDataSource<Bestellung>(response.data);
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

    this.dataSource.data = data.sort((a: Bestellung, b: Bestellung) => {
      const isAsc = sort.direction === 'asc';
      let key: string = sort.active.toString();

      return compare(`${a}.${key}`, `${b}.${key}`, isAsc);
    });
  }

  editRecord(record: Bestellung) {
    record.departmentId = this.departmentId;
    const dialogRef = this.dialog.open(BestellungEditComponentComponent, {
      width: '550px',
      height: '100vh',
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
        this.fetchDataByDepartmentId(this.departmentId); // Example method to reload data
      }
    });
  }
  addRecord() {
    const dialogRef = this.dialog.open(BestellungEditComponentComponent, {
      width: '550px',
      height: '100vh',
      data: {
        departmentId: this.departmentId
      },
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
        this.fetchDataByDepartmentId(this.departmentId);
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
