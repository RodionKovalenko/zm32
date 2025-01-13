import {Component} from '@angular/core';
import {NavigationEnd, Router, RouterOutlet} from "@angular/router";
import {routes} from "./app.routes";
import {AuthService} from "./auth.service";
import {NavigationMenuComponent} from "./navigation-menu/navigation-menu.component";
import {LoginComponent} from "./login/login.component";
import {NgIf} from "@angular/common";

@Component({
  selector: 'app-root',
  templateUrl: './app.component.html',
  styleUrls: ['./app.component.scss'],
  imports: [
    NavigationMenuComponent,
    LoginComponent,
    NgIf
  ]
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
