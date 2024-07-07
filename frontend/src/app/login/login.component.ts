import {Component, OnInit} from '@angular/core';
import {NavigationEnd, Router} from "@angular/router";
import {filter} from "rxjs";
import {AuthService} from "../auth.service";
import {MatDialog} from "@angular/material/dialog";
import {LoginErrorComponent} from "./login-error/login-error.component";

@Component({
    selector: 'app-login',
    templateUrl: './login.component.html',
    styleUrl: './login.component.css'
})
export class LoginComponent implements OnInit {
    title = 'IBA';
    mitarbeiterId: String = '';

    constructor(private router: Router, private authService: AuthService) {
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
        this.authService.login(Number(this.mitarbeiterId)).subscribe(
            loggedIn => {
                if (loggedIn) {
                    this.router.navigate(['/app-bestellliste']);
                }
            }
        );
    }
}
