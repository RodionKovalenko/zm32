import {Component, Inject, OnInit} from '@angular/core';
import {HttpService} from "../../services/http.service";
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from "@angular/material/dialog";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {Hersteller} from "../../models/Hersteller";
import {FormArray, FormBuilder, Validators} from "@angular/forms";

@Component({
    selector: 'app-hersteller-edit-component',
    templateUrl: './hersteller-edit-component.component.html',
    styleUrl: './hersteller-edit-component.component.css'
})
export class HerstellerEditComponentComponent implements OnInit {
    manufacturerForm: any;

    constructor(
        private httpService: HttpService,
        public dialogRef: MatDialogRef<HerstellerEditComponentComponent>,
        public dialog: MatDialog,
        private fb: FormBuilder,
        @Inject(MAT_DIALOG_DATA) public data: Hersteller
    ) {
    }

    ngOnInit(): void {
        // Initialize form with existing data if available
        this.manufacturerForm = this.fb.group({
            id: [this.data?.id || 0],
            name: [this.data?.name || '', Validators.required],
            standorte: this.fb.array([])
        });

        // Add existing standorte to the form if available
        if (this.data?.standorte) {
            this.data.standorte.forEach(st => this.addStandort(st));
        } else {
            this.addStandort(); // Add an initial Standort
        }
    }

    get standorte(): FormArray {
        return this.manufacturerForm.get('standorte') as FormArray;
    }

    addStandort(standort?: any): void {
        const standortGroup = this.fb.group({
            id: [standort?.id || 0],
            adresse: [standort?.adresse || '', Validators.required],
            ort: [standort?.ort || '', Validators.required],
            plz: [standort?.plz || ''],
            url: [standort?.url || '']
        });
        this.standorte.push(standortGroup);
    }

    removeStandort(index: number): void {
        this.standorte.removeAt(index);
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    onSubmit(): void {
        let url = this.httpService.get_baseUrl() + '/hersteller/save';

        if (this.manufacturerForm.valid) {
            console.log(this.manufacturerForm.value);

            this.httpService.get_httpclient().post(url, this.manufacturerForm.value).subscribe({
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
