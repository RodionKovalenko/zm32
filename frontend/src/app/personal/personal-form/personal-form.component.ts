import {Component, Inject, OnInit} from '@angular/core';
import {HttpService} from "../../services/http.service";
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from "@angular/material/dialog";
import {FormBuilder, Validators} from "@angular/forms";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {Mitarbeiter} from "../../models/Mitarbeiter";

@Component({
    selector: 'app-personal-form',
    templateUrl: './personal-form.component.html',
    styleUrl: './personal-form.component.css'
})

export class PersonalFormComponent implements OnInit {
    personalForm: any;

    constructor(
        private httpService: HttpService,
        public dialogRef: MatDialogRef<PersonalFormComponent>,
        public dialog: MatDialog,
        private fb: FormBuilder,
        @Inject(MAT_DIALOG_DATA) public data: Mitarbeiter
    ) {
    }

    ngOnInit(): void {
        // Initialize form with existing data if available
        this.personalForm = this.fb.group({
            id: [this.data?.id || 0],
            firstname: [this.data?.firstname || '', Validators.required],
            lastname: [this.data?.lastname || '', Validators.required],
            mitarbeiterId: [this.data?.mitarbeiterId || '', Validators.required],
        });
    }
    onNoClick(): void {
        this.dialogRef.close();
    }

    onSubmit(): void {
        let url = this.httpService.get_baseUrl() + '/user/save';

        if (this.personalForm.valid) {
            this.httpService.get_httpclient().post(url, this.personalForm.value).subscribe({
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