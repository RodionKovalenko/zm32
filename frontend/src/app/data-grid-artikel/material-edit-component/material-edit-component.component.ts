import {Component, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialog, MatDialogContent, MatDialogRef, MatDialogTitle} from "@angular/material/dialog";
import {Artikel} from "../../models/Artikel";
import {Lieferant} from "../../models/Lieferant";
import {HttpService} from "../../services/http.service";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {LieferantEditComponentComponent} from "../lieferant-edit-component/lieferant-edit-component.component";
import {Hersteller} from "../../models/Hersteller";
import {HerstellerEditComponentComponent} from "../hersteller-edit-component/hersteller-edit-component.component";
import {FormArray, FormBuilder, FormGroup, ReactiveFormsModule, Validators} from "@angular/forms";
import {IDropdownSettings, NgMultiSelectDropDownModule} from "ng-multiselect-dropdown";
import {DepartmentData} from "../../models/Department";
import {MatError, MatFormField, MatFormFieldModule} from "@angular/material/form-field";
import {NgForOf, NgIf} from "@angular/common";
import {MatInput, MatInputModule} from "@angular/material/input";
import {MatButton, MatIconButton} from "@angular/material/button";
import {MatTooltip} from "@angular/material/tooltip";
import {MatIcon} from "@angular/material/icon";
import {MatToolbar} from "@angular/material/toolbar";
import {floatValidator} from "../../shared/float_validator";
import {FocusOnClickDirective} from "../../shared/focus-on-click.directive";

@Component({
  selector: 'app-material-edit-component',
  templateUrl: './material-edit-component.component.html',
  styleUrl: './material-edit-component.component.css',
  imports: [
    MatDialogContent,
    ReactiveFormsModule,
    MatFormField,
    MatError,
    NgIf,
    MatInput,
    NgMultiSelectDropDownModule,
    MatIconButton,
    MatTooltip,
    MatIcon,
    NgForOf,
    MatButton,
    MatToolbar,
    MatFormFieldModule,
    MatInputModule,
    FocusOnClickDirective,
    MatDialogTitle
  ]
})
export class MaterialEditComponentComponent implements OnInit {
    lieferants: any[] = [];
    herstellers: any[] = [];
    departments: any[] = [];

    dropdownSettings: IDropdownSettings = {};
    dropdownDepartmentSettings: IDropdownSettings = {};
    singleSelectSettings: IDropdownSettings = {};
    artikelForm: any;

    childDialogOpened: boolean = false;
    dynamicTitleHerstellers: string[] = [];
    dynamicTitleLieferants: string[] = [];

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
            enableCheckAll: false,
            searchPlaceholderText: 'Suchen',
            selectAllText: 'Alle auswählen',
            unSelectAllText: 'Alle deaktivieren',
        };

        this.dropdownDepartmentSettings = {
            idField: 'id',
            textField: 'name',
            allowSearchFilter: true,
            searchPlaceholderText: 'Suchen',
            enableCheckAll: false,
        };

        this.singleSelectSettings = {
            singleSelection: true, // Set to true for single selection
            idField: 'id',
            textField: 'name',
            itemsShowLimit: 3,
            allowSearchFilter: true,
        };

        this.artikelForm = this.fb.group({
            id: [this.data?.id || 0],
            name: [this.data?.name || '', Validators.required],
            url: [this.data?.url || ''],
            preis: [this.data?.preis || '', floatValidator],
            description: [this.data?.description || ''],
            departments: [this.data?.departments || [], Validators.required],
            lieferants: [this.data?.lieferants || []],
            herstellers: [this.data?.herstellers || []],
            artikelToHerstRefnummers: this.fb.array(
                (this.data?.artikelToHerstRefnummers || []).map(st => this.fb.group({
                    refnummer: [st.refnummer || '', Validators.required],
                    hersteller: [[st.hersteller || null]],
                    filteredHerstellers: [this.herstellers]
                }))
            ),
            artikelToLieferantBestellnummers: this.fb.array(
                (this.data?.artikelToLieferantBestellnummers || []).map(st => this.fb.group({
                    lieferant: [[st.lieferant || null]],
                    bestellnummer: [st.bestellnummer || ''],
                    filteredLieferants: [this.lieferants]
                }))
            ),
        });

        this.initDropdownFromData();
        this.markAllAsTouched();
    }

    get artikelToHerstRefnummers(): FormArray {
        return this.artikelForm.get('artikelToHerstRefnummers') as FormArray;
    }

    addArtikelToHerstRefnummers(value?: any): void {
        let herstellerFormLength = this.artikelToHerstRefnummers.controls.length;

        let valueGroup = this.fb.group({
            refnummer: [value?.refnummer || '', Validators.required],
            hersteller: [value?.hersteller || null],
            filteredHerstellers: [herstellerFormLength === 0 ? this.artikelForm.get('herstellers').value : this.artikelToHerstRefnummers.controls[herstellerFormLength - 1]?.get('filteredHerstellers')?.value],
        });

        valueGroup.get('hersteller')?.valueChanges.subscribe((selectedHersteller) => {
            this.filterHerstellerOptions();

            let index =  this.artikelToHerstRefnummers.length - 1;
            if (selectedHersteller && selectedHersteller.length > 0) {
                this.dynamicTitleHerstellers[index] = `REF-Nummer Hersteller "${selectedHersteller[0].name}"`;
            } else {
                this.dynamicTitleHerstellers[index] = `Hersteller REF-Nummer ${index + 1}`;
            }
        });

        this.artikelToHerstRefnummers.push(valueGroup);

        this.filterHerstellerOptions();
    }

    onHerstellerSelect(event: any) {
        this.filterHerstellerOptions();
    }
    onLieferantSelect(event: any) {
        this.filterLieferantOptions();
    }

    get artikelToLieferantBestellnummers(): FormArray {
        return this.artikelForm.get('artikelToLieferantBestellnummers') as FormArray;
    }

    addArtikelToLieferantBestellnummers(value?: any): void {
        let herstellerFormLength = this.artikelToLieferantBestellnummers.controls.length;

        const valueGroup = this.fb.group({
            lieferant: [value?.lieferant || null],
            bestellnummer: [value?.bestellnummer || ''],
            filteredLieferants: [herstellerFormLength === 0 ? this.lieferants :  this.artikelToLieferantBestellnummers.controls[herstellerFormLength - 1]?.get('filteredLieferants')?.value],
        });

        valueGroup.get('lieferant')?.valueChanges.subscribe((selectedLieferants) => {
            this.filterLieferantOptions();

            let index =  this.artikelToLieferantBestellnummers.length - 1;
            if (selectedLieferants && selectedLieferants.length > 0) {
                this.dynamicTitleLieferants[index] = `Bestellnummer Lieferant "${selectedLieferants[0].name}"`;
            } else {
                this.dynamicTitleLieferants[index] = `Bestellnummer ${index + 1}`;
            }
        });
        this.artikelToLieferantBestellnummers.push(valueGroup);

        this.filterLieferantOptions();
    }

    removeHerstellerRefNummer(index: number): void {
        this.artikelToHerstRefnummers.removeAt(index);
    }

    removeLiferantBestellnummer(index: number): void {
        this.artikelToLieferantBestellnummers.removeAt(index);
    }

    initDropdownFromData() {
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
        let url = this.httpService.get_baseUrl() + '/lieferant/get_lieferant';
        let mitarbeiterRequest = this.httpService.get_httpclient().get(url);
        mitarbeiterRequest.subscribe((response: any) => {
            this.lieferants = response.data;

            if (data && data.length > 0) {
                this.updateLieferantsDropdown(data);
            }
            this.filterLieferantOptions();
        });
    }

    loadHerstellers(data: any) {
        let url = this.httpService.get_baseUrl() + '/hersteller/get_hersteller';
        let mitarbeiterRequest = this.httpService.get_httpclient().get(url);
        mitarbeiterRequest.subscribe((response: any) => {
            this.herstellers = response.data;

            if (data && data.length > 0) {
                this.updateHerstellersDropdown(data);
            }

            this.filterHerstellerOptions();
        });
    }

    isOnlyOneLieferantSelected(): boolean {
        const selectedItems = this.artikelForm.get('lieferants')?.value || [];
        return selectedItems.length === 1;
    }

    isOnlyOneHerstellerSelected(): boolean {
        const selectedItems = this.artikelForm.get('herstellers')?.value || [];
        return selectedItems.length === 1;
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    onSubmit(): void {
        let url = this.httpService.get_baseUrl() + '/artikel/save';

        if (this.artikelForm.valid && !this.childDialogOpened) {
            const formValue = this.artikelForm.value;
            formValue.departments = formValue.departments.map((dept: any) => (dept.id));
            formValue.lieferants = formValue.lieferants.map((lieferant: any) => (lieferant.id));
            formValue.herstellers = formValue.herstellers.map((hersteller: any) => (hersteller.id));

            formValue.artikelToHerstRefnummers = formValue.artikelToHerstRefnummers.map((artikelToHersteller: any) => {
                return {
                    artikel: formValue.id || null,
                    hersteller: artikelToHersteller.hersteller[0].id,
                    refnummer: artikelToHersteller.refnummer
                }
            });

            formValue.artikelToLieferantBestellnummers = formValue.artikelToLieferantBestellnummers.map((artikelToLieferant: any) => {
                return {
                    artikel: formValue.id || null,
                    lieferant: artikelToLieferant.lieferant[0].id,
                    bestellnummer: artikelToLieferant.bestellnummer,
                }
            });

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
        Object.keys(this.artikelForm.controls).forEach(field => {
            const control = this.artikelForm.get(field);
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
            let selectedLieferants = this.artikelForm.get('lieferants')?.value || [];
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

        this.childDialogOpened = true;
        dialogRef.afterClosed().subscribe(result => {
            this.childDialogOpened = false;
            if (result) {
                this.loadLieferants(result.data);
            }
        });
    }

    addHersteller(edit: boolean = false) {
        let data: any = {};
        data.formTitle = 'Neuen Hersteller hinzufügen';

        if (edit) {
            let selectedHerstellers = this.artikelForm.get('herstellers')?.value || [];
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

        this.childDialogOpened = true;
        dialogRef.afterClosed().subscribe(result => {
            this.childDialogOpened = false;
            if (result) {
                this.loadHerstellers(result.data);
            }
        });
    }

    onDepartmentSelect(department: any) {
        let departments = this.artikelForm.get('departments').value;

        let index = departments.findIndex((d: any) => d.name === 'Alle');
        if (department.name === 'Alle') {
            departments = departments.filter((d: any) => {
                return d.name === 'Alle'
            });
            this.updateDepartmentsDropdown(departments);
        } else if (index !== -1) {
            departments = departments.filter((d: any) => {
                return d.name !== 'Alle'
            });
            this.updateDepartmentsDropdown(departments);
        }
    }

    updateDepartmentsDropdown(data: DepartmentData[]) {
        this.artikelForm.get('departments')?.setValue(data);
    }

    updateLieferantsDropdown(data: Lieferant[]) {
        this.artikelForm.get('lieferants')?.setValue(data);
    }

    updateHerstellersDropdown(data: Hersteller[]) {
        this.artikelForm.get('herstellers')?.setValue(data);
    }

    filterHerstellerOptions(): void {
        const selectedHerstellers = this.artikelToHerstRefnummers.controls
            .map((control: any) => { return control.get('hersteller').value; })
            .filter(value => value != null);

        this.artikelToHerstRefnummers.controls.forEach((control: any, index: number) => {
            const filteredHerstellers = this.artikelForm.get('herstellers').value.filter(
                (hersteller: any) => {
                    let isHerstellerSelected = false;
                    const currentHersteller = control.get('hersteller').value?.[0];

                    if (selectedHerstellers.length > 0) {
                        selectedHerstellers.forEach((selectedHersteller: any) => {
                            if (selectedHersteller[0]?.id === hersteller.id && hersteller.id !== currentHersteller?.id) {
                               isHerstellerSelected = true;
                            }
                        });
                    }

                    return !isHerstellerSelected;
                }
            );

            let selecterHerstellerExists = false;
            selectedHerstellers.forEach((selectedHersteller: any) => {
                filteredHerstellers.forEach((filteredHersteller: any) => {
                    if (selectedHersteller[0]?.id === filteredHersteller.id) {
                        selecterHerstellerExists = true;
                    }
                });
            });

            if (!selecterHerstellerExists && control.get('hersteller').value?.length > 0) {
                control.get('hersteller').setValue([]);
            }

            if (control.get('hersteller').value?.length > 0) {
                this.dynamicTitleHerstellers[index] = `REF-Nummer Hersteller "${control.get('hersteller').value[0].name}"`;
            } else {
                this.dynamicTitleHerstellers[index] = `Hersteller REF-Nummer ${index + 1}`;
            }

            return control.get('filteredHerstellers').setValue(filteredHerstellers);
        });
    }

    filterLieferantOptions(): void {
        const selectedLiferants = this.artikelToLieferantBestellnummers.controls
            .map((control: any) => { return control.get('lieferant').value; })
            .filter(value => value != null);

        this.artikelToLieferantBestellnummers.controls.forEach((control: any, index: number) => {
            const filteredLieferants = this.artikelForm.get('lieferants').value.filter(
                (lieferant: any) => {
                    let isLieferantSelected = false;
                    const currentLieferant = control.get('lieferant').value?.[0];

                    if (selectedLiferants.length > 0) {
                        selectedLiferants.forEach((selectedLieferant: any) => {
                            if (selectedLieferant[0]?.id === lieferant.id && lieferant.id !== currentLieferant?.id) {
                                isLieferantSelected = true;
                            }
                        });
                    }

                    return !isLieferantSelected;
                }
            );

            let selecterLieferantExists = false;
            selectedLiferants.forEach((selectedLiferant: any) => {
                filteredLieferants.forEach((filteredLieferant: any) => {
                    if (selectedLiferant[0]?.id === filteredLieferant.id) {
                        selecterLieferantExists = true;
                    }
                });
            });

            if (!selecterLieferantExists && control.get('lieferant').value?.length > 0) {
                control.get('lieferant').setValue([]);
            }

            if (control.get('lieferant').value?.length > 0) {
                this.dynamicTitleLieferants[index] = `Bestellnummer Lieferant "${control.get('lieferant').value[0].name}"`;
            } else {
                this.dynamicTitleLieferants[index] = `Bestellnummer ${index + 1}`;
            }

            return control.get('filteredLieferants').setValue(filteredLieferants);
        });
    }
}
