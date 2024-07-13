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
}
