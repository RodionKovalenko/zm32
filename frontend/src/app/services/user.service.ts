import { Injectable } from '@angular/core';

interface User {
    id: string;
    firstname: string;
    lastname: string;
    mitarbeiterId: number;
}

@Injectable({
    providedIn: 'root'
})
export class UserService {
    private userData: User | null = null;

    // Method to set user data
    setUser(user: User): void {
        this.userData = user;
    }

    // Method to get user data
    getUser(): User | null {
        return this.userData;
    }

    // Method to clear user data
    clearUser(): void {
        this.userData = null;
    }
}
