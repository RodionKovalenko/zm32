import {Component, OnInit} from '@angular/core';
import {UserService} from "../services/user.service";
import {MatToolbar} from "@angular/material/toolbar";
import {MatIcon} from "@angular/material/icon";
import {MatIconButton} from "@angular/material/button";
import {MatSidenav, MatSidenavContainer, MatSidenavModule} from "@angular/material/sidenav";
import {MatListItem, MatNavList} from "@angular/material/list";
import {ActivatedRoute, NavigationEnd, Router, RouterLink, RouterLinkActive, RouterOutlet} from "@angular/router";
import {filter} from "rxjs";

@Component({
  selector: 'app-navigation-menu',
  templateUrl: './navigation-menu.component.html',
  imports: [
    MatToolbar,
    MatIcon,
    MatIconButton,
    MatSidenavContainer,
    MatNavList,
    MatListItem,
    RouterLink,
    RouterLinkActive,
    RouterOutlet,
    MatSidenav,
    MatSidenavModule
  ],
  styleUrl: './navigation-menu.component.css'
})
export class NavigationMenuComponent implements OnInit {
  user: any;
  routeName: string = 'Bestellungen';

  constructor(private userService: UserService, private router: Router, private activatedRoute: ActivatedRoute) { }


  ngOnInit() {
    this.user = this.userService.getUser();
    // Listen for route changes and update the route name
    this.router.events.pipe(
      filter(event => event instanceof NavigationEnd)
    ).subscribe(() => {
      this.updateRouteName();
    });

    // Initialize route name when the component is loaded
    this.updateRouteName();
  }

  private updateRouteName() {
    // Access the current route and extract the route name
    const currentRoute = this.activatedRoute.snapshot.firstChild;
    if (currentRoute && currentRoute.data) {
      this.routeName = currentRoute.data['title'] || 'Bestellungen';  // Set the route name
    }
  }
}
