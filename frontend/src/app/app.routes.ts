import {RouterModule, Routes} from '@angular/router';
import {AppComponent} from "./app.component";
import {ArtikellisteComponent} from "./artikelliste/artikelliste.component";
import {NgModule} from "@angular/core";
import {LoginComponent} from "./login/login.component";
import {BestelllisteComponent} from "./bestellliste/bestellliste.component";
import {AuthGuard} from "./permission.service";
import {LogoutComponent} from "./logout/logout.component";
export const routes: Routes = [
    { path: '', component: AppComponent, canActivate: [AuthGuard], pathMatch: 'full' },
    { path: 'app-login', component: LoginComponent, canActivate: [AuthGuard] },
    { path: 'app-bestellliste', component: BestelllisteComponent, canActivate: [AuthGuard] },
    { path: 'app-artikelliste', component: ArtikellisteComponent, canActivate: [AuthGuard] },
    { path: 'app-logout', component: LogoutComponent, canActivate: [AuthGuard] },
    // Add more routes as needed
    { path: '**', redirectTo: '/' } // Handle any other routes with a redirect
];




@NgModule({
    imports: [RouterModule, RouterModule.forRoot(routes, { useHash: true })],
    exports: [RouterModule]
})

export class AppRoutingModule {
}