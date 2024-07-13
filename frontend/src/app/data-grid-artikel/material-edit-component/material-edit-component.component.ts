import {Component, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from "@angular/material/dialog";
import {Artikel} from "../../models/Artikel";
import {Lieferant} from "../../models/Lieferant";
import {HttpService} from "../../services/http.service";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {LieferantEditComponentComponent} from "../lieferant-edit-component/lieferant-edit-component.component";

@Component({
    selector: 'app-material-edit-component',
    templateUrl: './material-edit-component.component.html',

    styleUrl: './material-edit-component.component.css',
})
export class MaterialEditComponentComponent implements  OnInit{
    selectedLieferant: Lieferant = {
        formTitle: "", id: 0, name: 'test',
        lieferantStammdaten: {
            plz: '',
            ort: '',
            adresse: ''
        }
    };

    lieferants: Lieferant[] = [this.selectedLieferant];

    constructor(
        private httpService: HttpService,
        public dialogRef: MatDialogRef<MaterialEditComponentComponent>,
        public dialog: MatDialog,
        @Inject(MAT_DIALOG_DATA) public data: Artikel
    ) {
    }

    ngOnInit() {
        this.loadLieferants();
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

    onNoClick(): void {
        this.dialogRef.close();
    }
    save(): void {
        let url = this.httpService.get_baseUrl() + '/artikel/save';

        const orderData = {
            id: this.data?.id,
            name: this.data?.name,
            description: this.data?.description,
        };

        this.httpService.get_httpclient().post(url, orderData).subscribe({
            next: (response: any) => {
                if (response && response.success && Boolean(response?.success)) {
                    this.dialogRef.close(response);
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

    addLieferant() {
        let data = this.selectedLieferant;
        data.formTitle = 'Neuen Lieferant hinzufügen';

        const dialogRef = this.dialog.open(LieferantEditComponentComponent, {
            width: '550px',
            data,
            disableClose: true,
        });

        dialogRef.afterClosed().subscribe(result => {
            if (result) {
            }
        });
    }
}
