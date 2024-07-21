import {Component, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from "@angular/material/dialog";
import {DepartmentData} from "../../models/Department";
import {HttpService} from "../../services/http.service";
import {Lieferant} from "../../models/Lieferant";
import {Bestellung} from "../../models/Bestellung";
import {MaterialEditComponentComponent} from "../../data-grid-artikel/material-edit-component/material-edit-component.component";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {LieferantEditComponentComponent} from "../../data-grid-artikel/lieferant-edit-component/lieferant-edit-component.component";
import {AbstractControl, FormArray, FormBuilder, FormGroup, ValidationErrors, Validators} from "@angular/forms";
import {Hersteller} from "../../models/Hersteller";
import {IDropdownSettings} from "ng-multiselect-dropdown";
import {HerstellerEditComponentComponent} from "../../data-grid-artikel/hersteller-edit-component/hersteller-edit-component.component";
import {Artikel} from "../../models/Artikel";

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
    dropdownSettings: IDropdownSettings = {};
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
            this.data.artikels.forEach(st => this.addArtikels(st));
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
            this.data.lieferants.forEach(st => this.addLieferants(st));
        }
        if (this.data?.herstellers) {
            this.data.herstellers.forEach(st => this.addHerstellers(st));
        }
    }

    addArtikels(value?: any): void {
        const valueGroup = this.fb.group({
            id: [value?.id || 0],
            name: [value?.name || 0],
        });
        this.artikels.push(valueGroup);
    }

    addHerstellers(value?: any): void {
        const valueGroup = this.fb.group({
            id: [value?.id || 0],
            name: [value?.name || 0],
        });
        this.herstellers.push(valueGroup);
    }

    addLieferants(value?: any): void {
        const valueGroup = this.fb.group({
            id: [value?.id || 0],
            name: [value?.name || 0],
        });
        this.lieferants.push(valueGroup);
    }

    addDepartments(value?: any): void {
        const valueGroup = this.fb.group({
            id: [value?.id || 0],
            name: [value?.name || 0],
        });
        this.departments.push(valueGroup);
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

    isOnlyOneLieferantSelected(): boolean {
        return this.bestellungForm.get('lieferants').value.length === 1;
    }

    isOnlyOneHerstellerSelected(): boolean {
        return this.bestellungForm.get('herstellers').value.length === 1;
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

    loadHerstellers(data: any) {
        let artikelId = this.data?.id || 0;
        let url = this.httpService.get_baseUrl() + '/hersteller/' + artikelId;
        let mitarbeiterRequest = this.httpService.get_httpclient().get(url);
        mitarbeiterRequest.subscribe((response: any) => {
            this.herstellers = response.data;
            if (data && data.length > 0) {
                this.updateHerstellerDropdown(data);
            }
        });
    }

    loadArtikel() {
        let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/artikel/0');
        mitarbeiterRequest.subscribe((response: any) => {
            this.artikels = response.data;
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

    onNoClick(): void {
        this.dialogRef.close();
    }

    addArtikel(edit: boolean = false) {
        let data: any = {};
        data.formTitle = 'Neuen Artikel hinzufügen';

        if (edit) {
            let selectedHerstellers = this.bestellungForm.get('artikels')?.value || [];

            if (selectedHerstellers.length === 1) {
                data = this.artikels.find(l => l.id === selectedHerstellers[0].id) || {};
            }
            data.formTitle = 'Artikel bearbeiten';
        }

        const dialogRef = this.dialog.open(MaterialEditComponentComponent, {
            width: '550px',
            height: '100vh',
            data,
            disableClose: true,
        });

        dialogRef.afterClosed().subscribe(result => {
            if (result) {
                this.updateBestellungToArtikelsDropdown(result.data);
            }
        });
    }

    addHersteller(edit: boolean = false) {
        let data: any = {};
        data.formTitle = 'Neuen Hersteller hinzufügen';

        if (edit) {
            let selectedHerstellers = this.bestellungForm.get('herstellers')?.value || [];
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

    addLieferant(edit: boolean = false) {
        let data: any = {};
        data.formTitle = 'Neuen Lieferant hinzufügen';

        if (edit) {
            let selectedLieferants = this.bestellungForm.get('lieferants')?.value || [];
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

    onSubmit(): void {
        let url = this.httpService.get_baseUrl() + '/bestellung/save';

        if (this.bestellungForm.valid) {
            this.httpService.get_httpclient().post(url, this.bestellungForm.value).subscribe({
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

    updateBestellungToArtikelsDropdown(data: Artikel[]) {

        if (Array.isArray(this.artikels)) { // Ensure it is an array
            const index = this.artikels.findIndex(record => record.id === data[0].id);
            if (index !== -1) {
                // Update the record at the found index
                this.artikels[index] = data[0];
            }
        }
    }

    updateLieferantsDropdown(data: Lieferant[]) {
        this.bestellungForm.get('lieferants')?.setValue(data);
    }

    updateHerstellerDropdown(data: Hersteller[]) {
        this.bestellungForm.get('herstellers')?.setValue(data);
    }
}
