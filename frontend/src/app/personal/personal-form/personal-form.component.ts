import {Component, Inject, OnInit} from '@angular/core';
import {HttpService} from "../../services/http.service";
import {MAT_DIALOG_DATA, MatDialog, MatDialogContent, MatDialogRef, MatDialogTitle} from "@angular/material/dialog";
import {FormBuilder, ReactiveFormsModule, Validators} from "@angular/forms";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {Mitarbeiter} from "../../models/Mitarbeiter";
import {IDropdownSettings, NgMultiSelectDropDownModule} from "ng-multiselect-dropdown";
import {MatFormField, MatFormFieldModule} from "@angular/material/form-field";
import {MatInput} from "@angular/material/input";
import {MatToolbar} from "@angular/material/toolbar";
import {MatIcon} from "@angular/material/icon";
import {MatIconButton} from "@angular/material/button";
import {MatTooltip} from "@angular/material/tooltip";
import {NgIf} from "@angular/common";

@Component({
  selector: 'app-personal-form',
  templateUrl: './personal-form.component.html',
  imports: [
    ReactiveFormsModule,
    MatDialogTitle,
    NgMultiSelectDropDownModule,
    MatFormField,
    MatInput,
    MatToolbar,
    MatIcon,
    MatIconButton,
    MatTooltip,
    MatFormFieldModule,
    MatDialogContent,
    NgIf
  ],
  styleUrl: './personal-form.component.css'
})

export class PersonalFormComponent implements OnInit {
    personalForm: any;
    dropdownDepartmentSettings: IDropdownSettings = {};
    departments: any[] = [];

    constructor(
        private httpService: HttpService,
        public dialogRef: MatDialogRef<PersonalFormComponent>,
        public dialog: MatDialog,
        private fb: FormBuilder,
        @Inject(MAT_DIALOG_DATA) public data: Mitarbeiter
    ) {
    }

    ngOnInit(): void {
        this.loadDepartments();

        // Initialize form with existing data if available
        this.personalForm = this.fb.group({
            id: [this.data?.id || 0],
            firstname: [this.data?.firstname || '', Validators.required],
            lastname: [this.data?.lastname || '', Validators.required],
            mitarbeiterId: [this.data?.mitarbeiterId || '', Validators.required],
            departments: [this.data?.departments || [], Validators.required],
        });

        this.dropdownDepartmentSettings = {
            idField: 'id',
            textField: 'name',
            allowSearchFilter: true,
            enableCheckAll: false,
            searchPlaceholderText: 'Suchen',
        };
    }

    loadDepartments() {
        let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/department/get_departments');
        mitarbeiterRequest.subscribe((response: any) => {
            this.departments = response.data;

            if (this.data && this.data.id) {
                let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/user/get_user_departments/' + this.data.id);
                mitarbeiterRequest.subscribe((response: any) => {
                    this.personalForm.get('departments')?.setValue(response.data);
                });
            }
        });
    }

    onDepartmentSelect(department: any) {
        let departments = this.personalForm.get('departments').value;

        let index = departments.findIndex((d: any) => d.name === 'Alle');
        if (department.name === 'Alle') {
            departments = departments.filter((d: any) => {
                return d.name === 'Alle'
            });
            this.updateDeparmentsDropdown(departments);
        } else if (index !== -1) {
            departments = departments.filter((d: any) => {
                return d.name !== 'Alle'
            });
            this.updateDeparmentsDropdown(departments);
        }
    }

    updateDeparmentsDropdown(departments: any) {
        this.personalForm.get('departments')?.setValue(departments);
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    onSubmit(): void {
        let url = this.httpService.get_baseUrl() + '/user/save';

        if (this.personalForm.valid) {
            const formValue = this.personalForm.value;
            formValue.departments = formValue.departments.map((dept: any) => (dept.id));

            this.httpService.get_httpclient().post(url, formValue).subscribe({
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
