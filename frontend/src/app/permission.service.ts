import {inject, Injectable} from '@angular/core';
import {ActivatedRouteSnapshot, CanActivateFn, Router, RouterStateSnapshot} from '@angular/router';
import {AuthService} from './auth.service';
import {JwtHelperService} from "./services/jwt-helper.service";

@Injectable({
  providedIn: 'root',
})
class PermissionsService {
  constructor(private router: Router, private authService: AuthService, private jwtHelper: JwtHelperService) {
  }

  canActivate(next: ActivatedRouteSnapshot, state: RouterStateSnapshot): boolean {
    const isLoggedIn = this.authService.isLoggedIn();
    const token = localStorage.getItem('access_token');
    const istValidToken: boolean = !!token && !this.jwtHelper.isTokenExpired(token);

    if (!istValidToken) {
      this.router.navigate(['/app-login']);
      return false;
    }

    // Avoid redirect loops
    if (state.url === '/app-login' && isLoggedIn) {
      this.router.navigate(['/app-bestellliste']); // Redirect logged-in user away from login page
      return false;
    } else if (!isLoggedIn) {
      // Redirect to login if not logged in
      this.router.navigate(['/app-login']);
      return false;
    }

    return true;
  }
}

// Export the AuthGuard function
export const AuthGuard: CanActivateFn = (
  next: ActivatedRouteSnapshot,
  state: RouterStateSnapshot
): boolean => {
  return inject(PermissionsService).canActivate(next, state);
};
