import {Component, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from "@angular/material/dialog";
import {Artikel} from "../../models/Artikel";
import {Lieferant} from "../../models/Lieferant";
import {HttpService} from "../../services/http.service";

@Component({
    selector: 'app-material-edit-component',
    templateUrl: './material-edit-component.component.html',

    styleUrl: './material-edit-component.component.css',
})
export class MaterialEditComponentComponent implements  OnInit{
    lieferants: Lieferant[] = [{id: 0, name: 'test'}];
    selectedLieferant: Lieferant = {id: 0, name: 'test'};

    constructor(
        private httpService: HttpService,
        public dialogRef: MatDialogRef<MaterialEditComponentComponent>,
        @Inject(MAT_DIALOG_DATA) public data: Artikel
    ) {
    }

    ngOnInit() {
        this.loadLieferants();
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    loadLieferants() {
        let url = this.httpService.get_baseUrl() + '/lieferant';
        let mitarbeiterRequest = this.httpService.get_httpclient().get(url);
        mitarbeiterRequest.subscribe((response: any) => {
            this.lieferants = response.data;

            if (this.lieferants.length > 0) {
                this.selectedLieferant = this.lieferants[0];
            }
        });
    }

    onLieferantChange(newValue: Lieferant): void {
        this.selectedLieferant = newValue;
    }

    save(): void {
        this.dialogRef.close(this.data);
    }
}
