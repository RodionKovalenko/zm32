import {Component, OnInit} from '@angular/core';
import {HttpService} from "../services/http.service";
import {DepartmentData} from "../models/Department";

@Component({
  selector: 'app-artikelliste',
  templateUrl: './artikelliste.component.html',
  styleUrl: './artikelliste.component.css'
})
export class ArtikellisteComponent implements OnInit {
    departmentRecords: DepartmentData[] = [{id: 0, name: 'test', typ: 0}];
    selectedValue: DepartmentData = {id: 0, name: '', typ: 0};
    constructor(private httpService: HttpService) {
    }

    loadDepartments() {
        let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/department/get_departments');
        mitarbeiterRequest.subscribe((response: any) => {
            this.departmentRecords = response.data;
            this.selectedValue = this.departmentRecords[0];
        });
    }

    ngOnInit(): void {
        this.loadDepartments();
    }
}
