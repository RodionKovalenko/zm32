import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";

@Injectable({
    providedIn: 'root'
})
export class HttpService {
    //private baseUrl = 'https://iba-backend.ddev.site/api';
    //private baseUrl = 'https://localhost:4433' ;
    baseUrl = 'https://iba.local.de/api';
    loginURL = `${this.baseUrl}/login`;

    constructor(private httpClient: HttpClient) {
        this.baseUrl = window.location.href.split('/#')[0] + ':4444/api';
        this.loginURL = `${this.baseUrl}/login`;
    }

    get_httpclient() {
        return this.httpClient;
    }

    get_baseUrl() {
        return this.baseUrl;
    }

    loginMitarbeiter(mitarbeiterId: Number) {
        return this.httpClient.get(this.loginURL + '/' + mitarbeiterId);
    }
}


import {HttpInterceptor, HttpRequest, HttpHandler, HttpEvent} from '@angular/common/http';
import {Observable} from 'rxjs';

@Injectable()
export class HttpAppInterceptor implements HttpInterceptor {
    intercept(request: HttpRequest<any>, next: HttpHandler): Observable<HttpEvent<any>> {
        // Modify the request or response here
        return next.handle(request);
    }
}