import {Injectable} from '@angular/core';

@Injectable({
  providedIn: 'root'
})
export class JwtHelperService {
  constructor() {
  }

  // Method to decode the JWT token
  decodeToken(token: string): any {
    const parts = token.split('.');
    if (parts.length !== 3) {
      throw new Error('Token is invalid');
    }
    const payload = parts[1];
    return JSON.parse(atob(payload));  // Decode the payload (middle part of the JWT)
  }

  // Method to check if the token has expired
  isTokenExpired(token: string): boolean {
    try {
      const decodedToken = this.decodeToken(token);  // Decode the token to get the payload
      const expirationDate = new Date(decodedToken.exp * 1000);  // Convert expiration timestamp to Date
      return expirationDate < new Date();  // Check if the expiration date is in the past
    } catch (error) {
      console.error('Error decoding token', error);
      return true;  // If there's an error (e.g., invalid token), assume expired
    }
  }
  setToken(newToken: string) {
    localStorage.setItem('access_token', newToken);
  }
}
