import {Component, Inject, OnInit} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialog, MatDialogRef} from "@angular/material/dialog";
import {Artikel} from "../../models/Artikel";
import {Lieferant} from "../../models/Lieferant";
import {HttpService} from "../../services/http.service";
import {LoginErrorComponent} from "../../login/login-error/login-error.component";
import {LieferantEditComponentComponent} from "../lieferant-edit-component/lieferant-edit-component.component";
import {Hersteller} from "../../models/Hersteller";
import {HerstellerEditComponentComponent} from "../hersteller-edit-component/hersteller-edit-component.component";
import {DepartmentData} from "../../models/Department";
import {FormBuilder} from "@angular/forms";
import {IDropdownSettings} from "ng-multiselect-dropdown";

@Component({
    selector: 'app-material-edit-component',
    templateUrl: './material-edit-component.component.html',

    styleUrl: './material-edit-component.component.css',
})
export class MaterialEditComponentComponent implements OnInit {
    selectedLieferant: Lieferant = {
        formTitle: "", id: 0, name: 'test',
        lieferantStammdaten: [
            {
                plz: '',
                ort: '',
                adresse: ''
            }
        ]
    };

    selectedHersteller: Hersteller = {
        formTitle: "", id: 0, name: '',
        standorte: [
            {
                plz: '',
                ort: '',
                adresse: ''
            }
        ]
    };

    lieferants: Lieferant[] = [];
    herstellers: Hersteller[] = [];
    departments: DepartmentData[] = [];

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
            departments: [],
            lieferants: [],
            herstellers: []
        });
    }

    loadDepartments() {
        let mitarbeiterRequest = this.httpService.get_httpclient().get(this.httpService.get_baseUrl() + '/department/get_departments');
        mitarbeiterRequest.subscribe((response: any) => {
            this.departments = response.data;
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
        let artikelId = this.data?.id || 0;
        let url = this.httpService.get_baseUrl() + '/hersteller/' +  artikelId;
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
        const selectedItems = this.dropDownForm.get('herstellers')?.value || [];
        return selectedItems.length === 1;
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    save(): void {
        let url = this.httpService.get_baseUrl() + '/artikel/save';

        const orderData = {
            id: this.data?.id,
            name: this.data?.name,
            description: this.data?.description,
            artikelToDepartments: this.dropDownForm.get('myItems')?.value
        };

        this.httpService.get_httpclient().post(url, orderData).subscribe({
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

    addLieferant(edit: boolean = false) {
        let data = this.getDefaultLieferant();
        data.formTitle = 'Neuen Lieferant hinzufügen';

        if (edit) {
            let selectedLieferants = this.dropDownForm.get('lieferants')?.value || [];
            if (selectedLieferants.length === 1) {
                data = this.lieferants.find(l => l.id === selectedLieferants[0].id) || this.getDefaultLieferant();
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
        let data = this.getDefaultHersteller();
        data.formTitle = 'Neuen Hersteller hinzufügen';

        if (edit) {
            let selectedHerstellers = this.dropDownForm.get('herstellers')?.value || [];
            if (selectedHerstellers.length === 1) {
                data = this.herstellers.find(l => l.id === selectedHerstellers[0].id) || this.getDefaultHersteller();
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

    getDefaultHersteller(): Hersteller {
        return {
            formTitle: "", id: 0, name: '',
            standorte: [
                {
                    plz: '',
                    ort: '',
                    adresse: ''
                }
            ]
        };
    }

    getDefaultLieferant(): Lieferant {
        return {
            formTitle: "", id: 0, name: '',
            lieferantStammdaten: [
                {
                    plz: '',
                    ort: '',
                    adresse: ''
                }
            ]
        };
    }

    updateLieferantsDropdown(data: Lieferant[]) {
        this.dropDownForm.get('lieferants')?.setValue(data);
    }

    updateHerstellersDropdown(data: Hersteller[]) {
        this.dropDownForm.get('herstellers')?.setValue(data);
    }
}
