import {Injectable} from '@angular/core';
import {BehaviorSubject, Observable} from 'rxjs';
import {ibaApp} from "../environments/environment";
import {routes} from "./app.routes";
import {HttpService} from "./services/http.service";
import {Router} from "@angular/router";
import {LoginErrorComponent} from "./login/login-error/login-error.component";
import {MatDialog} from "@angular/material/dialog";
import {UserService} from "./services/user.service";
import {JwtHelperService} from "./services/jwt-helper.service";
import { map } from 'rxjs/operators';

@Injectable({
  providedIn: 'root'
})
export class AuthService {

  private loggedIn = new BehaviorSubject<boolean>(false);

  constructor(private httpService: HttpService, private router: Router, private dialog: MatDialog, private userService: UserService, private jwtHelper: JwtHelperService) {
  }

  login(mitarbeiterId: Number): Observable<boolean> {
    let loginRequest = this.httpService.loginMitarbeiter(mitarbeiterId);
    let isLoggedIn = false;
    let istJwtTokenValid = false;

    loginRequest.subscribe((response: any) => {
      if (ibaApp && response && response.data) {

        const token = response.data['jwt'] || localStorage.getItem('access_token');
        const refreshToken = response.data['refresh_token'] || localStorage.getItem('refresh_token');
        istJwtTokenValid = !!token && !this.jwtHelper.isTokenExpired(token);

        localStorage.setItem('access_token', token);
        localStorage.setItem('refresh_token', refreshToken);

        this.loggedIn.next(istJwtTokenValid);

        ibaApp.user = response.data['user'];
        this.router.config = routes;
        if (istJwtTokenValid) {
          this.router.navigateByUrl('/', {skipLocationChange: false}).then(() => {
            //this.router.navigate(['/app-artikelliste']);
            // this.router.navigate(['/app-bestelliste']);
            this.router.navigate(['/app-navigation-menu']);
          });

          this.userService.setUser(response.data['user']);

          isLoggedIn = true;
          this.loggedIn.next(true);
          localStorage.setItem('isLoggedIn', 'true');
          localStorage.setItem('mitarbeiterId', mitarbeiterId.toString());
        }
      } else {
        this.loggedIn.next(false);
        localStorage.setItem('isLoggedIn', 'false');
        localStorage.setItem('mitarbeiterId', '');

        this.dialog.open(LoginErrorComponent, {
          width: '450px',
          height: '150px',
          data: {
            title: 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut'
          }
        });
      }

      if (!istJwtTokenValid) {
        this.dialog.open(LoginErrorComponent, {
          width: '450px',
          height: '150px',
          data: {
            title: 'Keine Validierung von JWT-Token'
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

  public refreshToken(): Observable<string> {
    return this.httpService.refreshToken().pipe(
      map((response: any) => {
        const token = response.data['jwt'];
        localStorage.setItem('access_token', token);
        return token;
      })
    );
  }

  public getToken(): string {
    return localStorage.getItem('access_token') || '';
  }

  logout(): void {
    this.loggedIn.next(false);
    localStorage.removeItem('isLoggedIn');
    localStorage.removeItem('access_token');
    localStorage.removeItem('refresh_token');
  }

  isLoggedIn(): boolean {
    return this.loggedIn.value; // Directly return current value of loggedIn
  }
}
