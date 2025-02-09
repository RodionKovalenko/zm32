import {CUSTOM_ELEMENTS_SCHEMA, NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {FormsModule, ReactiveFormsModule} from '@angular/forms';

import {AgGridModule} from 'ag-grid-angular';
import {MatSortModule} from '@angular/material/sort';
import {MatTableModule} from '@angular/material/table';
import {MatDialogModule} from '@angular/material/dialog';
import {MatFormFieldModule} from '@angular/material/form-field';
import {MatInputModule} from '@angular/material/input';
import {MatButtonModule} from '@angular/material/button'; // Import MatButtonModule for MatButton
import {MatPaginator, MatPaginatorModule} from '@angular/material/paginator'; // Import MatPaginatorModule for MatPaginator

import {RouterOutlet} from "@angular/router";
import {BrowserModule} from "@angular/platform-browser";
import {AppComponent} from "./app.component";
import {LoginComponent} from "./login/login.component";
import {ArtikellisteComponent} from "./artikelliste/artikelliste.component";
import {MatIconModule} from "@angular/material/icon";
import {BrowserAnimationsModule} from "@angular/platform-browser/animations";
import {RegistrateUserComponent} from "./registrate-user/registrate-user.component";
import {MatToolbar} from "@angular/material/toolbar";
import {MatSidenav, MatSidenavContainer, MatSidenavContent} from "@angular/material/sidenav";
import {MatListItem, MatNavList} from "@angular/material/list";
import {DataGridArtikelComponent} from "./data-grid-artikel/data-grid-artikel.component";
import {MaterialEditComponentComponent} from "./data-grid-artikel/material-edit-component/material-edit-component.component";
import {DataGridBestellungenComponent} from "./data-grid-bestellungen/data-grid-bestellungen.component";
import {BestellungEditComponentComponent} from "./data-grid-bestellungen/bestellung-edit-component/bestellung-edit-component.component";
import {BestelllisteComponent} from "./bestellliste/bestellliste.component";
import {NavigationMenuComponent} from "./navigation-menu/navigation-menu.component";
import {LogoutComponent} from "./logout/logout.component";
import {LoginErrorComponent} from "./login/login-error/login-error.component";
import {MatTooltip} from "@angular/material/tooltip";
import {LieferantEditComponentComponent} from "./data-grid-artikel/lieferant-edit-component/lieferant-edit-component.component";
import {HerstellerEditComponentComponent} from "./data-grid-artikel/hersteller-edit-component/hersteller-edit-component.component";
import {NgMultiSelectDropDownModule} from "ng-multiselect-dropdown";
import {MatCheckbox} from "@angular/material/checkbox";
import {MatOption, MatSelect} from "@angular/material/select";
import {MatDatepicker, MatDatepickerInput, MatDatepickerModule, MatDatepickerToggle} from "@angular/material/datepicker";
import {MatNativeDateModule} from "@angular/material/core";
import {DepartmentsComponent} from "./departments/departments.component";
import {DepartmentGridComponent} from "./departments/department-grid/department-grid.component";
import {HerstellerGridComponent} from "./herstellers/hersteller-grid/hersteller-grid.component";
import {LieferantGridComponent} from "./lieferants/lieferant-grid/lieferant-grid.component";
import {PersonalGridComponent} from "./personal/personal-grid/personal-grid.component";
import {PersonalComponent} from "./personal/personal.component";
import {PersonalFormComponent} from "./personal/personal-form/personal-form.component";
import {DepartmentFormComponent} from "./departments/department-form/department-form.component";
import {HerstellersComponent} from "./herstellers/herstellers.component";
import {LieferantsComponent} from "./lieferants/lieferants.component";
import {FocusOnClickDirective} from './shared/focus-on-click.directive';
import {HttpService} from "./services/http.service";

export function tokenGetter() {
  return localStorage.getItem('access_token');
}

// Factory function to initialize HttpService and get the base URL
export function appInitializer(httpService: HttpService) {
  return () => httpService.getBaseUrl().then(baseUrl => {
    // Store baseUrl in a global variable, service, or a state management solution
    localStorage.setItem('apiBaseUrl', baseUrl); // Example: saving to localStorage
  });
}

@NgModule({
  declarations: [],
  imports: [
    AppComponent,
    LoginComponent,
    LogoutComponent,
    LoginErrorComponent,
    ArtikellisteComponent,
    RegistrateUserComponent,
    DataGridArtikelComponent,
    DataGridBestellungenComponent,
    MaterialEditComponentComponent,
    LieferantEditComponentComponent,
    BestellungEditComponentComponent,
    BestelllisteComponent,
    NavigationMenuComponent,
    HerstellerEditComponentComponent,
    DepartmentsComponent,
    DepartmentFormComponent,
    DepartmentGridComponent,
    PersonalGridComponent,
    PersonalFormComponent,
    PersonalComponent,
    HerstellersComponent,
    HerstellerGridComponent,
    LieferantGridComponent,
    LieferantsComponent,
    BrowserModule,
    CommonModule,
    AgGridModule,
    MatSortModule,
    MatTableModule,
    MatDialogModule,
    MatFormFieldModule,
    MatInputModule,
    MatTableModule,
    MatPaginator,
    MatIconModule,
    MatButtonModule,
    MatPaginatorModule,
    FormsModule,
    RouterOutlet,
    BrowserAnimationsModule,
    MatToolbar,
    MatSidenavContent,
    MatSidenavContainer,
    MatNavList,
    MatListItem,
    MatSidenav,
    MatTooltip,
    ReactiveFormsModule,
    NgMultiSelectDropDownModule.forRoot(),
    MatCheckbox,
    MatSelect,
    MatOption,
    MatDatepickerToggle,
    MatDatepicker,
    MatDatepickerInput,
    MatDatepickerInput,
    MatDatepickerModule,
    MatNativeDateModule,
    MatFormFieldModule,
    FocusOnClickDirective,
    JwtModule.forRoot({
      config: {
        tokenGetter: tokenGetter,
        allowedDomains: [localStorage.getItem('apiBaseUrl')],// Specify your API domain
        disallowedRoutes: [localStorage.getItem('apiBaseUrl') + '/api/login'],
      },
    }),
  ],
  exports: [
    PersonalGridComponent
  ],
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class AppModule {
}

