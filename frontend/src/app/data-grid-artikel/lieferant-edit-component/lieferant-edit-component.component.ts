import {Component, Inject} from '@angular/core';
import {HttpService} from "../../services/http.service";
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from "@angular/material/dialog";
import {Lieferant} from "../../models/Lieferant";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";

@Component({
    selector: 'app-lieferant-edit-component',
    templateUrl: './lieferant-edit-component.component.html',
    styleUrl: './lieferant-edit-component.component.css'
})
export class LieferantEditComponentComponent {
    constructor(
        private httpService: HttpService,
        public dialogRef: MatDialogRef<LieferantEditComponentComponent>,
        public dialog: MatDialog,
        @Inject(MAT_DIALOG_DATA) public data: Lieferant
    ) {
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    save(): void {
        let url = this.httpService.get_baseUrl() + '/lieferant/save';

        this.httpService.get_httpclient().post(url, this.data).subscribe({
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

    get plz() {
        if (this.data.lieferantStammdaten && this.data.lieferantStammdaten[0]) {
            return this.data.lieferantStammdaten[0].plz || '';
        } else {
            this.data.lieferantStammdaten = [{}];
        }

        return '';
    }

    set plz(value: string) {
        this.initializeStammdaten();
        if (this.data?.lieferantStammdaten[0]) {
            this.data.lieferantStammdaten[0].plz = value;
        }
    }

    get ort() {
        if (this.data.lieferantStammdaten && this.data.lieferantStammdaten[0]) {
            return this.data?.lieferantStammdaten[0]?.ort || '';
        } else {
            this.data.lieferantStammdaten = [{}];
        }
        return '';
    }
    set ort(value: string) {
        this.initializeStammdaten();
        if (this.data?.lieferantStammdaten[0]) {
            this.data.lieferantStammdaten[0].ort = value;
        }
    }

    get adresse() {
        if (this.data.lieferantStammdaten && this.data.lieferantStammdaten[0]) {
            return this.data?.lieferantStammdaten[0]?.adresse || '';
        } else {
            this.data.lieferantStammdaten = [{}];
        }
        return '';
    }
    set adresse(value: string) {
        this.initializeStammdaten();
        if (this.data.lieferantStammdaten[0]) {
            this.data.lieferantStammdaten[0].adresse = value;
        }
    }

    initializeStammdaten() {
        if (!this.data?.lieferantStammdaten[0]) {
            this.data.lieferantStammdaten = [{}];
        }
    }
}
