import {Component} from '@angular/core';
import {NavigationEnd, Router, RouterOutlet} from "@angular/router";
import {routes} from "./app.routes";
import {LoginComponent} from "./login/login.component";
import {NgIf} from "@angular/common";
import {AuthService} from "./auth.service";

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    styleUrls: ['./app.component.scss'],
})
export class AppComponent{
    title = 'IBA';

    isLoginVisible = false;
    isArtikellisteVisible = false;

    constructor(private router: Router, private authService: AuthService) {
        this.router.config = routes;
        this.router.navigate(['/app-login']);

        this.router.events.subscribe(event => {
            if (event instanceof NavigationEnd) {
                this.isLoginVisible = this.router.url === '/app-login' || this.router.url === '/';
                this.isArtikellisteVisible = this.router.url === '/app-artikelliste';
            }
        });
    }

    isLoggedIn(): boolean {
        return this.authService.isLoggedIn();
    }
}
