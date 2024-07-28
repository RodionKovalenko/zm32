import {Component, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from "@angular/material/dialog";
import {HttpService} from "../../services/http.service";
import {Lieferant} from "../../models/Lieferant";
import {Bestellung} from "../../models/Bestellung";
import {MaterialEditComponentComponent} from "../../data-grid-artikel/material-edit-component/material-edit-component.component";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {AbstractControl, FormArray, FormBuilder, FormGroup, ValidationErrors, Validators} from "@angular/forms";
import {Hersteller} from "../../models/Hersteller";
import {IDropdownSettings} from "ng-multiselect-dropdown";
import {Artikel} from "../../models/Artikel";
import {DepartmentData} from "../../models/Department";

@Component({
    selector: 'app-bestellung-edit-component',
    templateUrl: './bestellung-edit-component.component.html',
    styleUrl: './bestellung-edit-component.component.css'
})
export class BestellungEditComponentComponent implements OnInit {
    herstellers: any[] = [];
    lieferants: any[] = [];
    artikels: any[] = [];
    departments: any[] = [];

    childDialogOpened: boolean = false;

    dropdownSettings: IDropdownSettings = {};
    dropdownDepartmentSettings: IDropdownSettings = {};
    singleSelectSettings: IDropdownSettings = {};
    bestellungForm: any;

    constructor(private httpService: HttpService,
                public dialogRef: MatDialogRef<BestellungEditComponentComponent>,
                @Inject(MAT_DIALOG_DATA) public data: Bestellung,
                private fb: FormBuilder,
                public dialog: MatDialog) {
    }

    ngOnInit(): void {
        this.loadDepartments();
        this.loadArtikel();

        this.dropdownSettings = {
            idField: 'id',
            textField: 'name',
            allowSearchFilter: true,
            enableCheckAll: true,
            searchPlaceholderText: 'Suchen',
            selectAllText: 'Alle auswählen',
            unSelectAllText: 'Alle deaktivieren'
        };

        this.dropdownDepartmentSettings = {
            idField: 'id',
            textField: 'name',
            allowSearchFilter: true,
            enableCheckAll: false,
            searchPlaceholderText: 'Suchen',
        };

        this.singleSelectSettings = {
            singleSelection: true, // Set to true for single selection
            idField: 'id',
            textField: 'name',
            itemsShowLimit: 3,
            allowSearchFilter: true
        };

        this.bestellungForm = this.fb.group({
            id: [this.data?.id || 0],
            description: [this.data?.description || ''],
            descriptionZusatz: [this.data?.descriptionZusatz || ''],
            amount: [this.data?.amount || ''],
            preis: [this.data?.preis || '', this.floatValidator],
            artikels: [this.data?.artikels || [], Validators.required],
            departments: [this.data?.departments || [], Validators.required],
            herstellers: [this.data?.herstellers || []],
            lieferants: [this.data?.lieferants || []],
            herstellerStandorte: [this.data?.herstellerStandorte || []],
            lieferantStandorte: [this.data?.lieferantStandorte || []]
        });

        if (this.data?.artikels) {
            this.data.artikels.forEach(st => this.addArtikel(st));
        }

        if (this.data?.departments) {
            this.data.departments.forEach(st => this.addDepartments(st));
        }

        // Add existing standorte to the form if available
        if (this.data?.lieferantStandorte) {
            this.data.lieferantStandorte.forEach(st => this.addLieferantStandorte(st));
        }

        if (this.data?.herstellerStandorte) {
            this.data.herstellerStandorte.forEach(st => this.addHerstellerStandorte(st));
        }

        if (this.data?.lieferants) {
            this.lieferants = this.data.lieferants;
            this.data.lieferants.forEach(st => this.addLieferants(st));
        }
        if (this.data?.herstellers) {
            this.herstellers = this.data.herstellers;
            this.data.herstellers.forEach(st => this.addHerstellers(st));
        }

        this.markAllAsTouched();

        if (this.data.id && this.data.artikels && this.data.artikels.length > 0) {
            this.loadArtikelFullData(this.data.artikels[0].id).then((data) => {
                this.updateDropdowns(data);
            });
        }
    }

    addArtikel(value?: any): void {
        const valueGroup = this.fb.group({
            id: [value?.id || 0],
            name: [value?.name || 0],
        });
        this.artikels.push(valueGroup);
    }

    addHerstellers(value?: any): void {
        if (value.id) {
            const valueGroup = this.fb.group({
                id: [value?.id || 0],
                name: [value?.name || 0],
            });
            this.herstellers.push(valueGroup);
        }
    }

    addLieferants(value?: any): void {
        if (value.id) {
            const valueGroup = this.fb.group({
                id: [value?.id || 0],
                name: [value?.name || 0],
            });
            this.lieferants.push(valueGroup);
        }
    }

    addDepartments(value?: any): void {
        if (value.id) {
            const valueGroup = this.fb.group({
                id: [value?.id || 0],
                name: [value?.name || 0],
            });
            this.departments.push(valueGroup);
        }
    }

    get lieferantStandorte(): FormArray {
        return this.bestellungForm.get('lieferantStandorte') as FormArray;
    }

    addLieferantStandorte(value?: any): void {
        const valueGroup = this.fb.group({
            id: [value?.id || 0],
            name: [value?.name || 0],
        });
        this.lieferantStandorte.push(valueGroup);
    }

    get herstellerStandorte(): FormArray {
        return this.bestellungForm.get('herstellerStandorte') as FormArray;
    }

    addHerstellerStandorte(value?: any): void {
        const valueGroup = this.fb.group({
            id: [value?.id || 0],
            name: [value?.name || 0],
        });
        this.herstellerStandorte.push(valueGroup);
    }

    isOnlyOneArtikelSelected(): boolean {
        return this.bestellungForm.get('artikels').value.length === 1;
    }

    loadDepartments() {
        let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/department/get_departments');
        mitarbeiterRequest.subscribe((response: any) => {
            this.departments = response.data;
        });
    }

    loadArtikel() {
        let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/artikel/0');
        mitarbeiterRequest.subscribe((response: any) => {
            this.artikels = response.data;
        });
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    addArtikelRecord(edit: boolean = false) {
        this.childDialogOpened = true;

        let data: any = {};
        data.formTitle = 'Neuen Artikel hinzufügen';

        if (edit) {
            let selectedHerstellers = this.bestellungForm.get('artikels')?.value || [];

            if (selectedHerstellers.length === 1) {
                data = this.artikels.find(l => l.id === selectedHerstellers[0].id) || {};
            }
            data.formTitle = 'Artikel bearbeiten';
        }

        if (data.id) {
            this.loadArtikelFullData(data.id).then((data) => this.openArtikelEditDialog(data[0]));
        } else {
            this.openArtikelEditDialog(data);
        }
    }

    openArtikelEditDialog(data: any) {
        const dialogRef = this.dialog.open(MaterialEditComponentComponent, {
            width: '550px',
            height: '100vh',
            data,
            disableClose: true,
        });

        dialogRef.afterClosed().subscribe(result => {
            this.childDialogOpened = false;
            if (result) {
                this.updateDropdowns(result.data);
            }
        });
    }

    updateDropdowns(data: any) {
        this.updateBestellungToArtikelsDropdown(data);
    }

    loadArtikelFullData(id: number): Promise<any> {
        return new Promise((resolve, reject) => {
            let url = this.httpService.get_baseUrl() + '/artikel/get_by_id/' + id;

            let artikelRequest = this.httpService.get_httpclient().get(url);
            artikelRequest.subscribe((response: any) => {
                if (response.success) {
                    resolve(response.data);
                    this.updateDropdowns(response.data);
                    response.data[0].formTitle = 'Artikel bearbeiten';
                } else {
                    resolve({});
                }
            });
        });
    }

    onSubmit(): void {
        let url = this.httpService.get_baseUrl() + '/bestellung/save';

        if (this.bestellungForm.valid && !this.childDialogOpened) {
            const formValue = this.bestellungForm.value;
            formValue.departments = formValue.departments.map((dept: any) => (dept.id));
            formValue.lieferants = formValue.lieferants.map((lieferant: any) => (lieferant.id));
            formValue.herstellers = formValue.herstellers.map((hersteller: any) => (hersteller.id));
            formValue.artikels = formValue.artikels.map((artikel: any) => (artikel.id));
            formValue.mitarbeiterId = localStorage.getItem('mitarbeiterId');

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
        Object.keys(this.bestellungForm.controls).forEach(field => {
            const control = this.bestellungForm.get(field);
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

    private floatValidator(control: AbstractControl): ValidationErrors | null {
        const value = control.value;
        const floatRegex = /^[+-]?\d+(\.\d+)?$/;
        if (value && !floatRegex.test(value)) {
            return {invalidFloat: true};
        }
        return null;
    }

    onArtikelChange(event: any) {
        this.loadArtikelFullData(event.id).then((data) => {
            this.updateDropdowns(data);
        });
    }

    updateBestellungToArtikelsDropdown(data: Artikel[]) {
        if (Array.isArray(this.artikels)) { // Ensure it is an array
            const index = this.artikels.findIndex(record => record.id === data[0].id);
            if (index !== -1) {
                // Update the record at the found index
                this.artikels[index] = data[0];
                // Update the dropdown

                this.lieferants = data[0].lieferants || [];
                this.herstellers = data[0].herstellers || [];
                this.departments = data[0].departments || [];

                if (data[0].departments && data[0].departments.length > 0) {
                    this.updateDeparmentsDropdown(data[0].departments);
                } else {
                    this.updateDeparmentsDropdown([]);
                }
                if (data[0].lieferants && data[0].lieferants.length > 0) {
                    this.updateLieferantsDropdown([data[0].lieferants[0]]);
                } else {
                    this.updateLieferantsDropdown([]);
                }
                if (data[0].herstellers && data[0].herstellers.length > 0) {
                    this.updateHerstellerDropdown([data[0].herstellers[0]]);
                } else {
                    this.updateHerstellerDropdown([]);
                }
            }
        }
    }

    onDepartmentSelect(department: any) {
        let departments = this.bestellungForm.get('departments').value;

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

    updateDeparmentsDropdown(data: DepartmentData[]) {
        this.bestellungForm.get('departments')?.setValue(data);
    }

    updateLieferantsDropdown(data: Lieferant[]) {
        this.bestellungForm.get('lieferants')?.setValue(data);
    }

    updateHerstellerDropdown(data: Hersteller[]) {
        this.bestellungForm.get('herstellers')?.setValue(data);
    }
}
