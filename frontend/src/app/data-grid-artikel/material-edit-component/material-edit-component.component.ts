import {Component, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from "@angular/material/dialog";
import {Artikel} from "../../models/Artikel";
import {Lieferant} from "../../models/Lieferant";
import {HttpService} from "../../services/http.service";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {LieferantEditComponentComponent} from "../lieferant-edit-component/lieferant-edit-component.component";
import {Hersteller} from "../../models/Hersteller";
import {HerstellerEditComponentComponent} from "../hersteller-edit-component/hersteller-edit-component.component";
import {FormArray, FormBuilder, FormGroup, Validators} from "@angular/forms";
import {IDropdownSettings} from "ng-multiselect-dropdown";
import {DepartmentData} from "../../models/Department";

@Component({
    selector: 'app-material-edit-component',
    templateUrl: './material-edit-component.component.html',
    styleUrl: './material-edit-component.component.css',
})
export class MaterialEditComponentComponent implements OnInit {
    lieferants: any[] = [];
    herstellers: any[] = [];
    departments: any[] = [];

    dropdownSettings: IDropdownSettings = {};
    dropDownForm: any;

    constructor(
        private httpService: HttpService,
        public dialogRef: MatDialogRef<MaterialEditComponentComponent>,
        public dialog: MatDialog,
        private fb: FormBuilder,
        @Inject(MAT_DIALOG_DATA) public data: Artikel
    ) {
    }

    ngOnInit() {
        this.loadDepartments();
        this.loadLieferants([]);
        this.loadHerstellers([]);

        this.dropdownSettings = {
            idField: 'id',
            textField: 'name',
            allowSearchFilter: true,
            enableCheckAll: true,
            searchPlaceholderText: 'Suchen',
            selectAllText: 'Alle auswählen',
            unSelectAllText: 'Alle deaktivieren'
        };

        this.dropDownForm = this.fb.group({
            id: [this.data?.id || 0],
            name: [this.data?.name || '', Validators.required],
            description: [this.data?.description || ''],
            departments: [this.data?.departments || [], Validators.required],
            lieferants: [this.data?.lieferants || []],
            herstellers: [this.data?.herstellers || []]
        });

        if (this.data?.departments) {
            this.data.departments.forEach(st => this.addDepartment(st));
        }

        // Add existing standorte to the form if available
        if (this.data?.lieferants) {
            this.data.lieferants.forEach(st => this.addLieferants(st));
        }

        if (this.data?.herstellers) {
            this.data.herstellers.forEach(st => this.addHerstellers(st));
        }

        this.markAllAsTouched();
    }

    addLieferants(value?: any): void {
        const valueGroup = this.fb.group({
            lieferant: [value?.id || 0]
        });
        this.lieferants.push(valueGroup);
    }

    addDepartment(value?: any): void {
        const valueGroup = this.fb.group({
            id: [value?.id || 0],
            name: [value?.name || '']
        });
        this.departments.push(valueGroup);
    }

    addHerstellers(value?: any): void {
        const valueGroup = this.fb.group({
            hersteller: [value?.id || 0],
        });
        this.herstellers.push(valueGroup);
    }

    loadDepartments() {
        let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/department/get_departments');
        mitarbeiterRequest.subscribe((response: any) => {
            this.departments = response.data;
            if (this.data?.departments) {
                this.updateDepartmentsDropdown(this.data.departments);
            }
        });
    }

    loadLieferants(data: any) {
        let url = this.httpService.get_baseUrl() + '/lieferant';
        let mitarbeiterRequest = this.httpService.get_httpclient().get(url);
        mitarbeiterRequest.subscribe((response: any) => {
            this.lieferants = response.data;

            if (data && data.length > 0) {
                this.updateLieferantsDropdown(data);
            }
        });
    }

    loadHerstellers(data: any) {
        let url = this.httpService.get_baseUrl() + '/hersteller/0';
        let mitarbeiterRequest = this.httpService.get_httpclient().get(url);
        mitarbeiterRequest.subscribe((response: any) => {
            this.herstellers = response.data;
            if (data && data.length > 0) {
                this.updateHerstellersDropdown(data);
            }
        });
    }

    isOnlyOneLieferantSelected(): boolean {
        const selectedItems = this.dropDownForm.get('lieferants')?.value || [];
        return selectedItems.length === 1;
    }

    isOnlyOneHerstellerSelected(): boolean {
        const selectedItems = this.dropDownForm.get('herstellerToArtikels')?.value || [];
        return selectedItems.length === 1;
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    onSubmit(): void {
        let url = this.httpService.get_baseUrl() + '/artikel/save';

        if (this.dropDownForm.valid) {
            const formValue = this.dropDownForm.value;
            formValue.departments = formValue.departments.map((dept: any) => (dept.id));
            formValue.lieferants = formValue.lieferants.map((lieferant: any) => (lieferant.id));
            formValue.herstellers = formValue.herstellers.map((hersteller: any) => (hersteller.id));

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

    private markAllAsTouched() {
        Object.keys(this.dropDownForm.controls).forEach(field => {
            const control = this.dropDownForm.get(field);
            if (control) { // Ensure control is not null
                if (control instanceof FormGroup) {
                    this.markAllAsTouchedRecursive(control);
                } else {
                    control.markAsTouched({onlySelf: true});
                }
            }
        });
    }

    private markAllAsTouchedRecursive(group: FormGroup) {
        Object.keys(group.controls).forEach(controlName => {
            const control = group.get(controlName);
            if (control) { // Ensure control is not null
                if (control instanceof FormGroup) {
                    this.markAllAsTouchedRecursive(control);
                } else {
                    control.markAsTouched({onlySelf: true});
                }
            }
        });
    }

    addLieferant(edit: boolean = false) {
        let data: any = {};
        data.formTitle = 'Neuen Lieferant hinzufügen';

        if (edit) {
            let selectedLieferants = this.dropDownForm.get('lieferants')?.value || [];
            if (selectedLieferants.length === 1) {
                data = this.lieferants.find(l => l.id === selectedLieferants[0].id) || {};
            }
            data.formTitle = 'Lieferant bearbeiten';
        }

        const dialogRef = this.dialog.open(LieferantEditComponentComponent, {
            width: '550px',
            height: '100vh',
            data,
            disableClose: true,
        });

        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.loadLieferants(result.data);
            }
        });
    }

    addHersteller(edit: boolean = false) {
        let data: any = {};
        data.formTitle = 'Neuen Hersteller hinzufügen';

        if (edit) {
            let selectedHerstellers = this.dropDownForm.get('herstellers')?.value || [];
            if (selectedHerstellers.length === 1) {
                data = this.herstellers.find(l => l.id === selectedHerstellers[0].id) || {};
            }
            data.formTitle = 'Hersteller bearbeiten';
        }

        const dialogRef = this.dialog.open(HerstellerEditComponentComponent, {
            width: '550px',
            height: '100vh',
            data,
            disableClose: true,
        });

        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.loadHerstellers(result.data);
            }
        });
    }

    updateDepartmentsDropdown(data: DepartmentData[]) {
        this.dropDownForm.get('departments')?.setValue(data);
    }

    updateLieferantsDropdown(data: Lieferant[]) {
        this.dropDownForm.get('lieferants')?.setValue(data);
    }

    updateHerstellersDropdown(data: Hersteller[]) {
        this.dropDownForm.get('herstellers')?.setValue(data);
    }
}
