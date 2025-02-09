import {HttpInterceptor, HttpRequest, HttpHandler, HttpEvent} from '@angular/common/http';
import {Observable, throwError, EMPTY} from 'rxjs';
import { catchError, switchMap, take } from 'rxjs/operators';
import {Injectable} from '@angular/core';
import {AuthService} from '../auth.service';
import {Router} from '@angular/router';
import {JwtHelperService} from './jwt-helper.service';

@Injectable({
  providedIn: 'root',
})
export class AuthInterceptorService implements HttpInterceptor {
  constructor(private authService: AuthService, private router: Router, private jwtHelperService: JwtHelperService) {
  }

  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    if (req.url.includes('/api/login')) {
      return next.handle(req); // Don't add the Authorization header for login requests
    }
    // Get the JWT token from AuthService
    const token = this.authService.getToken();

    // Check if the token is expired or invalid (you can implement your logic here)
    const isTokenExpired = this.jwtHelperService.isTokenExpired(token);

    // If the token is expired, refresh it
    if (isTokenExpired) {
      return this.authService.refreshToken().pipe(
        take(1), // Make sure we only take one value from the refresh observable
        switchMap((newToken: string) => {
          // Update the token in the service
          this.jwtHelperService.setToken(newToken);

          // Clone the request and add the new token
          const clonedRequest = req.clone({
            setHeaders: {
              Authorization: `Bearer ${newToken}`,
            },
          });

          // Continue with the request after refreshing the token
          return next.handle(clonedRequest);
        }),
        catchError(() => {
          // If refresh fails, log the user out and navigate to login
          this.authService.logout();
          this.router.navigate(['/login']);
          return EMPTY; // Don't propagate further errors
        })
      );
    }

    // If the token is valid, just proceed with the original request
    const clonedRequest = req.clone({
      setHeaders: {
        Authorization: `Bearer ${token}`,
      },
    });

    return next.handle(clonedRequest).pipe(
      catchError((error) => {
        // Handle other errors (not 401)
        return throwError(error);
      })
    );
  }
}
