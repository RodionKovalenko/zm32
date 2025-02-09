import { HttpInterceptor, HttpRequest, HttpHandler, HttpEvent } from '@angular/common/http';
import { Observable, throwError, EMPTY } from 'rxjs';
import { catchError, switchMap } from 'rxjs/operators';
import { Injectable } from '@angular/core';
import { AuthService } from '../auth.service';
import { Router } from '@angular/router';

@Injectable({
  providedIn: 'root',
})
export class AuthInterceptorService implements HttpInterceptor {
  constructor(private authService: AuthService, private router: Router) {}

  intercept(req: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
    if (req.url.includes('/api/login')) {
      return next.handle(req); // Don't add the Authorization header for login requests
    }
    // Get the JWT token from AuthService
    const token = this.authService.getToken();

    // If token is present, clone the request and attach the token
    const clonedRequest = token
      ? req.clone({
        setHeaders: {
          Authorization: `Bearer ${token}`,
        },
      })
      : req;

    return next.handle(clonedRequest).pipe(
      catchError((error) => {
        // If the error status is 401 (unauthorized), attempt to refresh the token
        if (error.status === 401) {
          return this.authService.refreshToken().pipe(
            switchMap((newToken: string) => {
              // Retry the failed request with the new token
              const newRequest = req.clone({
                setHeaders: {
                  Authorization: `Bearer ${newToken}`,
                },
              });
              return next.handle(newRequest);
            }),
            catchError(() => {
              // If refresh fails, log the user out and navigate to login
              this.authService.logout();
              this.router.navigate(['/login']);
              return EMPTY; // Don't propagate further errors
            })
          );
        }

        // For other errors, just propagate them
        return throwError(error);
      })
    );
  }
}
