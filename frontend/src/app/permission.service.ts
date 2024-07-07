import {inject, Injectable} from "@angular/core";
import {ActivatedRouteSnapshot, CanActivateFn, Router, RouterStateSnapshot} from "@angular/router";
import {AuthService} from "./auth.service";

@Injectable({
    providedIn: 'root'
})
class PermissionsService {

    constructor(private router: Router, private authService: AuthService) {}

    canActivate(next: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
        let isLoggedIn =  this.authService.isLoggedIn();

        if (state.url === '/app-login') {
            if (isLoggedIn) {
                this.router.navigate(['/app-artikelliste']); // Redirect logged-in user away from login page
                return false;
            } else {
                return true;
            }
        } else {
            if (!isLoggedIn) {
                this.router.navigate(['/app-login']); // Redirect to login page if not logged in
                return false;
            } else {
                return true; // Allow access to other pages if logged in
            }
        }
    }
}

export const AuthGuard: CanActivateFn = (next: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean => {
    return inject(PermissionsService).canActivate(next, state);
}