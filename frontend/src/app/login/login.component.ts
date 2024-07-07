import {Component} from '@angular/core';
import {HttpService} from "../services/http.service";
import {NavigationEnd, Router} from "@angular/router";
import {filter} from "rxjs";
import {ibaApp} from "../../enviroments/environment";
import {routes} from "../app.routes";

@Component({
    selector: 'app-login',
    templateUrl: './login.component.html',
    styleUrl: './login.component.css'
})
export class LoginComponent {
    title = 'IBA';

    constructor(private httpService: HttpService, private router: Router) {
    }

    ngOnInit() {
        this.router.events.pipe(
            filter(event => event instanceof NavigationEnd)
        ).subscribe(() => {
            const routes = this.router.config;
            console.log(routes);
        });
    }

    onLoginClick() {
        let mitarbeiterRequest = this.httpService.loginMitarbeiter(1);

        mitarbeiterRequest.subscribe((response: any) => {
            if (ibaApp && response && response.data) {
                ibaApp.user = response.data[0];
                this.router.config = routes;
                this.router.navigateByUrl('/', {skipLocationChange: false}).then(() => {
                    //this.router.navigate(['/app-artikelliste']);
                    // this.router.navigate(['/app-bestelliste']);
                     this.router.navigate(['/app-navigation-menu']);
                });
            }
        });
    }
}
