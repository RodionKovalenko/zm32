import {Injectable} from '@angular/core';
import {HttpClient} from "@angular/common/http";
import { environment } from '../../environments/environment';

@Injectable({
  providedIn: 'root'
})
export class HttpService {
  private baseUrl = 'https://127.0.0.1:53713/api';
  private loginURL = '';
  private refreshJWTURL = '';
  //baseUrl = 'https://iba.local.de/api';

  constructor(private httpClient: HttpClient) {
    let prod = environment.production;

    if (prod) {
      let baselocation = window.location.href.split('/#')[0];
      baselocation = baselocation.trim();

      let lastChar = baselocation.charAt(baselocation.length - 1);

      if (lastChar === '/') {
        baselocation = baselocation.slice(0, -1);
      }
      this.baseUrl = baselocation + '/api';
    }

    this.loginURL = `${this.baseUrl}/login`;
    this.refreshJWTURL = `${this.baseUrl}/login/refresh-jwt-token`;
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

  refreshToken() {
    const refresh_token = localStorage.getItem('refresh_token');  // The API endpoint URL
    return this.httpClient.post(this.refreshJWTURL, {refresh_token: refresh_token});
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
