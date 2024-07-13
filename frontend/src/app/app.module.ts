import {CUSTOM_ELEMENTS_SCHEMA, NgModule} from '@angular/core';
import {CommonModule} from '@angular/common';
import {FormsModule} from '@angular/forms';

import {AgGridModule} from 'ag-grid-angular';
import {MatSortModule} from '@angular/material/sort';
import {MatTableModule} from '@angular/material/table';
import {MatDialogModule} from '@angular/material/dialog';
import {MatFormFieldModule} from '@angular/material/form-field';
import {MatInputModule} from '@angular/material/input';
import {MatButtonModule} from '@angular/material/button'; // Import MatButtonModule for MatButton
import {MatPaginator, MatPaginatorModule} from '@angular/material/paginator'; // Import MatPaginatorModule for MatPaginator

import {HttpAppInterceptor} from './services/http.service';
import {AppRoutingModule} from './app.routes';
import {RouterOutlet} from "@angular/router";
import {BrowserModule} from "@angular/platform-browser";
import {AppComponent} from "./app.component";
import {HTTP_INTERCEPTORS, provideHttpClient, withInterceptorsFromDi} from "@angular/common/http";
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
import {FlexModule} from "@angular/flex-layout";
import {NavigationMenuComponent} from "./navigation-menu/navigation-menu.component";
import {LogoutComponent} from "./logout/logout.component";
import {LoginErrorComponent} from "./login/login-error/login-error.component";
import {MatTooltip} from "@angular/material/tooltip";
import {LieferantEditComponentComponent} from "./data-grid-artikel/lieferant-edit-component/lieferant-edit-component.component";

@NgModule({
    declarations: [
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
        NavigationMenuComponent
    ],
    imports: [
        BrowserModule,
        CommonModule,
        AppRoutingModule,
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
        FlexModule,
        MatTooltip
    ],
    providers: [
        provideHttpClient(withInterceptorsFromDi()),
        // Add your interceptors to the HTTP_INTERCEPTORS multi-provider
        {
            provide: HTTP_INTERCEPTORS,
            useClass: HttpAppInterceptor,
            multi: true
        }
    ],
    bootstrap: [AppComponent],
    exports: [
    ],
    schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class AppModule {
}
