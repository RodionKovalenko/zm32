import { Component } from '@angular/core';
import {LieferantGridComponent} from "./lieferant-grid/lieferant-grid.component";

@Component({
  selector: 'app-lieferants',
  templateUrl: './lieferants.component.html',
  imports: [
    LieferantGridComponent
  ],
  styleUrl: './lieferants.component.css'
})
export class LieferantsComponent {

}
