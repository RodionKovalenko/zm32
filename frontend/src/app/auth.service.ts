import {Injectable} from '@angular/core';
import {BehaviorSubject, Observable} from 'rxjs';
import {ibaApp} from "../enviroments/environment";
import {routes} from "./app.routes";
import {HttpService} from "./services/http.service";
import {Router} from "@angular/router";
import {LoginErrorComponent} from "./login/login-error/login-error.component";

@Injectable({
    providedIn: 'root'
})
export class AuthService {

    private loggedIn = new BehaviorSubject<boolean>(false);

    constructor(private httpService: HttpService, private router: Router, private dialog: MatDialog) {
    }

    login(mitarbeiterId: Number): Observable<boolean> {
        let mitarbeiterRequest = this.httpService.loginMitarbeiter(mitarbeiterId);
        let isLoggedIn = false;

        mitarbeiterRequest.subscribe((response: any) => {
            if (ibaApp && response && response.data) {
                ibaApp.user = response.data[0];
                this.router.config = routes;
                this.router.navigateByUrl('/', {skipLocationChange: false}).then(() => {
                    //this.router.navigate(['/app-artikelliste']);
                    // this.router.navigate(['/app-bestelliste']);
                    this.router.navigate(['/app-navigation-menu']);
                });

                isLoggedIn = true;
                this.loggedIn.next(true);
                localStorage.setItem('isLoggedIn', 'true');
            } else {
                this.dialog.open(LoginErrorComponent, {
                    width: '450px',
                    height: '150px',
                    data: {
                        title: 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut'
                    }
                });
            }

            return this.loggedIn.asObservable();
        });

        // Implement actual authentication logic (e.g., API call)
        // For demonstration purposes, use a simple check
        if (!isLoggedIn) {
            this.loggedIn.next(false);
            localStorage.removeItem('isLoggedIn');
        }

        return this.loggedIn.asObservable();
    }

    logout(): void {
        this.loggedIn.next(false);
        localStorage.removeItem('isLoggedIn');
    }

    isLoggedIn(): boolean {
        return this.loggedIn.value; // Directly return current value of loggedIn
    }
}