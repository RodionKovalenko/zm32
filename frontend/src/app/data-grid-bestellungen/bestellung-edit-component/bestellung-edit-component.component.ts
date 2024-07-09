import {Component, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from "@angular/material/dialog";
import {DepartmentData} from "../../models/Department";
import {HttpService} from "../../services/http.service";
import {Artikel} from "../../models/Artikel";
import {Lieferant} from "../../models/Lieferant";
import {Bestellung} from "../../models/Bestellung";

@Component({
    selector: 'app-bestellung-edit-component',
    templateUrl: './bestellung-edit-component.component.html',
    styleUrl: './bestellung-edit-component.component.css'
})
export class BestellungEditComponentComponent implements OnInit {
    constructor(private httpService: HttpService,
                public dialogRef: MatDialogRef<BestellungEditComponentComponent>,
                @Inject(MAT_DIALOG_DATA) public data: Bestellung) {
    }

    artikels: Artikel[] = [{
        id: 0, name: 'test',
        quantity: 0,
        description: ''
    }];
    selectedArtikel: Artikel = {
        id: 0, name: 'test',
        quantity: 0,
        description: ''
    };
    departments: DepartmentData[] = [{id: 0, name: 'test', typ: 0}];
    selectedDepartment: DepartmentData = {id: 0, name: '', typ: 0};

    lieferants: Lieferant[] = [{id: 0, name: 'test'}];
    selectedLieferant: Lieferant = {id: 0, name: 'test'};

    ngOnInit(): void {
        this.loadDepartments();
        this.loadLieferants();
    }

    loadDepartments() {
        let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/department/get_departments');
        mitarbeiterRequest.subscribe((response: any) => {
            this.departments = response.data;
            if (this.departments.length > 0) {

                if (this.data.departmentId) {
                    this.departments.forEach((department) => {
                        if (department.id === this.data.departmentId) {
                            this.selectedDepartment = department;
                        }
                    });
                } else {
                    this.selectedDepartment = this.departments[0];
                }

                this.loadArtikel(this.selectedDepartment);
            }
        });
    }

    loadArtikel(department: DepartmentData) {
        let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/artikel/' + department.id);
        mitarbeiterRequest.subscribe((response: any) => {
            this.artikels = response.data;
            if (this.artikels.length > 0) {
                if (this.data.artikel) {
                    this.artikels.forEach((artikel) => {
                        if (artikel.id === this.data?.artikel?.id) {
                            this.selectedArtikel = artikel;
                        }
                    });
                } else {
                    this.selectedArtikel = this.artikels[0];
                }
            }
        });
    }

    loadLieferants() {
        let url = this.httpService.get_baseUrl() + '/lieferant';
        if (this.selectedArtikel && this.selectedArtikel.id !== 0) {
            url += '/' + this.selectedArtikel.id;
        }
        let mitarbeiterRequest = this.httpService.get_httpclient().get(url);
        mitarbeiterRequest.subscribe((response: any) => {
            this.lieferants = response.data;

            if (this.lieferants.length > 0) {
                this.selectedLieferant = this.lieferants[0];
            }
        });
    }

    onDepartmentChange(newValue: DepartmentData): void {
        this.selectedDepartment = newValue;
        this.loadArtikel(this.selectedDepartment);
    }

    onArtikelChange(newValue: Artikel): void {
        this.selectedArtikel = newValue;
    }

    onLieferantChange(newValue: Lieferant): void {
        this.selectedLieferant = newValue;
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    addArtikel() {

    }

    save(): void {
        let url = this.httpService.get_baseUrl() + '/bestellung/save';

        const orderData = {
            id: this.data?.id,
            department: this.selectedDepartment.id,
            artikel: this.selectedArtikel.id,
            mitarbeiterId: localStorage.getItem('mitarbeiterId'),
            description: this.data?.description,
            descriptionZusatz: this.data?.descriptionZusatz,
            preis: this.data?.preis,
            amount: this.data?.amount,
            lieferantId: this.selectedLieferant?.id
        };
        this.httpService.get_httpclient().post(url, orderData).subscribe({
            next: (response) => {
                this.dialogRef.close(response);
            },
            error: (err) => {
                console.error('Error creating order:', err);
            }
        });
    }
}
