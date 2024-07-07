import {RouterModule, Routes} from '@angular/router';
import {AppComponent} from "./app.component";
import {ArtikellisteComponent} from "./artikelliste/artikelliste.component";
import {NgModule} from "@angular/core";
import {LoginComponent} from "./login/login.component";
import {BestelllisteComponent} from "./bestellliste/bestellliste.component";
export const routes: Routes = [
    { path: '', redirectTo: '/app-login', pathMatch: 'full' }, // Corrected path
    { path: 'app-root', component: AppComponent },
    { path: 'app-artikelliste', component: ArtikellisteComponent },
    { path: 'app-bestelliste', component: BestelllisteComponent },
    { path: '**', component: LoginComponent }
];


@NgModule({
    imports: [RouterModule, RouterModule.forRoot(routes, { useHash: true })],
    exports: [RouterModule]
})

export class AppRoutingModule {
}