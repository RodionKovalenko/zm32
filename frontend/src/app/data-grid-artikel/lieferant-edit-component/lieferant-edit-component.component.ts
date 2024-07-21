import {Component, Inject, OnInit} from '@angular/core';
import {HttpService} from "../../services/http.service";
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from "@angular/material/dialog";
import {Lieferant} from "../../models/Lieferant";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {FormArray, FormBuilder, Validators} from "@angular/forms";

@Component({
    selector: 'app-lieferant-edit-component',
    templateUrl: './lieferant-edit-component.component.html',
    styleUrl: './lieferant-edit-component.component.css'
})
export class LieferantEditComponentComponent implements OnInit {
    lieferantForm: any;

    constructor(
        private httpService: HttpService,
        public dialogRef: MatDialogRef<LieferantEditComponentComponent>,
        public dialog: MatDialog,
        private fb: FormBuilder,
        @Inject(MAT_DIALOG_DATA) public data: Lieferant
    ) {
    }

    ngOnInit(): void {
        // Initialize form with existing data if available
        this.lieferantForm = this.fb.group({
            id: [this.data?.id || 0],
            name: [this.data?.name || '', Validators.required],
            lieferantStammdaten: this.fb.array([])
        });

        // Add existing standorte to the form if available
        if (this.data?.lieferantStammdaten) {
            this.data.lieferantStammdaten.forEach(st => this.addLieferantStammdaten(st));
        } else {
            this.addLieferantStammdaten(); // Add an initial Standort
        }
    }

    get lieferantStammdaten(): FormArray {
        return this.lieferantForm.get('lieferantStammdaten') as FormArray;
    }

    addLieferantStammdaten(standort?: any): void {
        const standortGroup = this.fb.group({
            id: [standort?.id || 0],
            adresse: [standort?.adresse || '', Validators.required],
            ort: [standort?.ort || '', Validators.required],
            plz: [standort?.plz || ''],
            telefon: [standort?.telefon || ''],
            url: [standort?.url || '']
        });
        this.lieferantStammdaten.push(standortGroup);
    }

    removeLieferantStammdaten(index: number): void {
        this.lieferantStammdaten.removeAt(index);
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    onSubmit(): void {
        let url = this.httpService.get_baseUrl() + '/lieferant/save';

        if (this.lieferantForm.valid) {
            this.httpService.get_httpclient().post(url, this.lieferantForm.value).subscribe({
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
}
