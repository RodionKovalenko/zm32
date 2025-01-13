import {Component, OnInit} from '@angular/core';
import {NavigationEnd, Router} from "@angular/router";
import {filter} from "rxjs";
import {AuthService} from "../auth.service";
import {LoginErrorComponent} from "./login-error/login-error.component";
import {MatDialog} from "@angular/material/dialog";
import {MatFormField} from "@angular/material/form-field";
import {FormsModule} from "@angular/forms";
import {MatIcon} from "@angular/material/icon";
import {MatIconButton} from "@angular/material/button";
import {MatInput} from "@angular/material/input";
import {NgForOf, NgIf} from "@angular/common";

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  imports: [
    MatFormField,
    FormsModule,
    MatIcon,
    MatIconButton,
    MatInput,
    NgIf,
    NgForOf
  ],
  styleUrl: './login.component.css'
})
export class LoginComponent implements OnInit {
    title = 'IBA';
    mitarbeiterId: String = '';
    numbers: number[] = [1, 2, 3, 4, 5, 6, 7, 8, 9, 0, 10];

    constructor(private router: Router, private authService: AuthService, private dialog: MatDialog) {
    }

    ngOnInit() {
        this.router.events.pipe(
            filter(event => event instanceof NavigationEnd)
        ).subscribe(() => {
            const routes = this.router.config;
        });
    }

    onKeyup(event: KeyboardEvent) {
        if (event.key === 'Enter') {
            event.preventDefault();
            this.onLoginClick();
        }
    }

    preventEnter(event: KeyboardEvent) {
        if (event.key === 'Enter') {
            event.preventDefault();
        }
    }
    clearInput() {
        this.mitarbeiterId = '';
    }

    onLoginChange(event: any) {
        this.mitarbeiterId = event.target.value;

        if (this.mitarbeiterId.length === 4) {
            this.onLoginClick();
        }
    }
    onNumberClick(number: number) {
        if (number === 10) {
            this.onLoginClick();
            return;
        }
        this.mitarbeiterId += number.toString();

        if (this.mitarbeiterId.length === 4) {
            this.onLoginClick();
        }
    }

    onLoginClick() {
        if (this.mitarbeiterId.length < 4) {
            this.dialog.open(LoginErrorComponent, {
                width: '450px',
                height: '150px',
                data: {
                    title: 'Es müssen mindestens 4 Ziffern eingegeben werden.'
                }
            });

            return;
        }

        let loginStr = Number(this.mitarbeiterId.substring(1, 4));

        this.authService.login(Number(loginStr)).subscribe(
            loggedIn => {
                if (loggedIn) {
                    this.router.navigate(['/app-bestellliste']);
                }
            }
        );
    }
}
