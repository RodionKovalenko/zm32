import {RouterModule, Routes} from '@angular/router';
import {AppComponent} from "./app.component";
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
  {path: 'app-bestellliste', component: BestelllisteComponent, canActivate: [AuthGuard]},
  {path: 'app-artikelliste', component: ArtikellisteComponent, canActivate: [AuthGuard]},
  {path: 'app-departments', component: DepartmentsComponent, canActivate: [AuthGuard]},
  {path: 'app-herstellers', component: HerstellersComponent, canActivate: [AuthGuard]},
  {path: 'app-lieferants', component: LieferantsComponent, canActivate: [AuthGuard]},
  {path: 'app-personal', component: PersonalComponent, canActivate: [AuthGuard]},
  {path: 'app-logout', component: LogoutComponent, canActivate: [AuthGuard]},
];


@NgModule({
  imports: [RouterModule, RouterModule.forRoot(routes, {useHash: true})],
  exports: [RouterModule]
})

export class AppRoutingModule {
}
