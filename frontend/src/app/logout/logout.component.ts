import {Component, OnInit} from '@angular/core';
import {filter} from "rxjs";
import {AuthService} from "../auth.service";

@Component({
    selector: 'app-logout',
    templateUrl: './logout.component.html',
    styleUrl: './logout.component.css'
})
export class LogoutComponent implements OnInit {

    public constructor(private authService: AuthService) {

    }
    ngOnInit() {
        this.authService.logout();
    }
}
