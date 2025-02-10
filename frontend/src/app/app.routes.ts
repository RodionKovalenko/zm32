import {RouterModule, Routes} from '@angular/router';
import {ArtikellisteComponent} from "./artikelliste/artikelliste.component";
import {NgModule} from "@angular/core";
import {LoginComponent} from "./login/login.component";
import {BestelllisteComponent} from "./bestellliste/bestellliste.component";
import {AuthGuard} from "./permission.service";
import {LogoutComponent} from "./logout/logout.component";
import {DepartmentsComponent} from "./departments/departments.component";
import {HerstellersComponent} from "./herstellers/herstellers.component";
import {LieferantsComponent} from "./lieferants/lieferants.component";
import {PersonalComponent} from "./personal/personal.component";

export const routes: Routes = [
  {path: 'app-login', component: LoginComponent},
  {path: 'app-bestellliste', component: BestelllisteComponent, data: {title: 'Bestellungen'}, canActivate: [AuthGuard]},
  {path: 'app-artikelliste', component: ArtikellisteComponent, data: {title: 'Artikelliste'}, canActivate: [AuthGuard]},
  {path: 'app-departments', component: DepartmentsComponent, data: {title: 'Abteilungen'}, canActivate: [AuthGuard]},
  {path: 'app-herstellers', component: HerstellersComponent, data: {title: 'Hersteller'}, canActivate: [AuthGuard]},
  {path: 'app-lieferants', component: LieferantsComponent, data: {title: 'Lieferanten'}, canActivate: [AuthGuard]},
  {path: 'app-personal', component: PersonalComponent, data: {title: 'Mitarbeiter'}, canActivate: [AuthGuard]},
  {path: 'app-logout', component: LogoutComponent, canActivate: [AuthGuard]},
  {path: '**', component: BestelllisteComponent, pathMatch: 'full', canActivateChild: [false]}
];


@NgModule({
  imports: [RouterModule, RouterModule.forRoot(routes, {useHash: true})],
  exports: [RouterModule]
})

export class AppRoutingModule {
}
