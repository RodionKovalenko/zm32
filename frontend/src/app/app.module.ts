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
import {DataGridComponent} from "./data-grid/data-grid.component";
import {MaterialEditComponentComponent} from "./data-grid/material-edit-component/material-edit-component.component";
import {MatIconModule} from "@angular/material/icon";
import {BrowserAnimationsModule} from "@angular/platform-browser/animations";
import {RegistrateUserComponent} from "./registrate-user/registrate-user.component";

@NgModule({
    declarations: [
        AppComponent,
        LoginComponent,
        ArtikellisteComponent,
        RegistrateUserComponent,
        DataGridComponent,
        MaterialEditComponentComponent
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
    schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class AppModule {
}
