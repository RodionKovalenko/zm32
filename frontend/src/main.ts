/*
*  Protractor support is deprecated in Angular.
*  Protractor is used in this example for compatibility with Angular documentation tools.
*/
import { bootstrapApplication } from '@angular/platform-browser';
import { AppComponent } from './app/app.component';
import {HTTP_INTERCEPTORS, provideHttpClient, withInterceptorsFromDi} from "@angular/common/http";
import {HttpAppInterceptor} from "./app/services/http.service";
import {DateAdapter, MAT_DATE_FORMATS} from "@angular/material/core";
import {MY_DATE_FORMATS} from "./app/models/DateFormat";
import {CustomDateAdapter} from "./app/data-adapters/date.adapter";
import {provideAnimations} from "@angular/platform-browser/animations";
import {provideRouter} from "@angular/router";
import {routes} from "./app/app.routes";
import { HashLocationStrategy, LocationStrategy } from '@angular/common';
import { AuthInterceptorService } from './app/services/auth-interceptor.service';

bootstrapApplication(AppComponent, {
  providers: [
    provideRouter(routes),
    { provide: LocationStrategy, useClass: HashLocationStrategy },
    provideAnimations(),
    provideHttpClient(withInterceptorsFromDi()),
    // Add your interceptors to the HTTP_INTERCEPTORS multi-provider
    {
      provide: HTTP_INTERCEPTORS,
      useClass: AuthInterceptorService,
      multi: true  // Ensure multiple interceptors can be used
    },
    {
      provide: HTTP_INTERCEPTORS,
      useClass: HttpAppInterceptor,
      multi: true
    },
    {provide: MAT_DATE_FORMATS, useValue: MY_DATE_FORMATS},
    {provide: DateAdapter, useClass: CustomDateAdapter},
  ],
}).catch(err => console.error(err));
