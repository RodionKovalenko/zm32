import {Component, Inject, OnInit} from '@angular/core';
import {HttpService} from "../../services/http.service";
import {MAT_DIALOG_DATA, MatDialog, MatDialogContent, MatDialogRef, MatDialogTitle} from "@angular/material/dialog";
import {FormBuilder, ReactiveFormsModule, Validators} from "@angular/forms";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {DepartmentData} from "../../models/Department";
import {MatFormFieldModule} from "@angular/material/form-field";
import {MatInputModule} from "@angular/material/input";
import {NgIf} from "@angular/common";
import {MatToolbar} from "@angular/material/toolbar";
import {MatIconButton} from "@angular/material/button";
import {MatTooltip} from "@angular/material/tooltip";
import {MatIcon} from "@angular/material/icon";
@Component({
  selector: 'app-department-form',
  templateUrl: './department-form.component.html',
  imports: [
    MatDialogTitle,
    MatFormFieldModule,
    MatInputModule,
    ReactiveFormsModule,
    NgIf,
    MatToolbar,
    MatIconButton,
    MatTooltip,
    MatIcon,
    MatDialogContent,
  ],
  styleUrl: './department-form.component.css'
})
export class DepartmentFormComponent implements OnInit {
    departmentForm: any;

    constructor(
        private httpService: HttpService,
        public dialogRef: MatDialogRef<DepartmentFormComponent>,
        public dialog: MatDialog,
        private fb: FormBuilder,
        @Inject(MAT_DIALOG_DATA) public data: DepartmentData
    ) {
    }

    ngOnInit(): void {
        // Initialize form with existing data if available
        this.departmentForm = this.fb.group({
            id: [this.data?.id || 0],
            name: [this.data?.name || '', Validators.required],
            typ: [this.data?.typ || ''],
        });
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    onSubmit(): void {
        let url = this.httpService.get_baseUrl() + '/department/save';

        if (this.departmentForm.valid) {
            this.httpService.get_httpclient().post(url, this.departmentForm.value).subscribe({
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

