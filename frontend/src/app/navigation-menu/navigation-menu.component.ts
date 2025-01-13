import {Component, OnInit} from '@angular/core';
import {UserService} from "../services/user.service";
import {MatToolbar} from "@angular/material/toolbar";
import {MatIcon} from "@angular/material/icon";
import {MatIconButton} from "@angular/material/button";
import {MatSidenav, MatSidenavContainer, MatSidenavModule} from "@angular/material/sidenav";
import {MatListItem, MatNavList} from "@angular/material/list";
import {RouterLink, RouterLinkActive, RouterOutlet} from "@angular/router";
import {BestelllisteComponent} from "../bestellliste/bestellliste.component";
import {ArtikellisteComponent} from "../artikelliste/artikelliste.component";
import {HerstellerGridComponent} from "../herstellers/hersteller-grid/hersteller-grid.component";
import {HerstellersComponent} from "../herstellers/herstellers.component";
import {LieferantsComponent} from "../lieferants/lieferants.component";
import {LieferantGridComponent} from "../lieferants/lieferant-grid/lieferant-grid.component";

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
  constructor(private userService: UserService) {}

  ngOnInit(): void {
    this.user = this.userService.getUser();
  }
}
