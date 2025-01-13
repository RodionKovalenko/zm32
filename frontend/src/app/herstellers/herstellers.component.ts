import { Component } from '@angular/core';
import {HerstellerGridComponent} from "./hersteller-grid/hersteller-grid.component";

@Component({
  selector: 'app-herstellers',
  templateUrl: './herstellers.component.html',
  imports: [
    HerstellerGridComponent
  ],
  styleUrl: './herstellers.component.css'
})
export class HerstellersComponent {

}
