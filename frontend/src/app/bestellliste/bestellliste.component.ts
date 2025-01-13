import {Component} from '@angular/core';
import {DataGridBestellungenComponent} from "../data-grid-bestellungen/data-grid-bestellungen.component";
@Component({
  selector: 'app-bestellliste',
  templateUrl: './bestellliste.component.html',
  imports: [
    DataGridBestellungenComponent
  ],
  styleUrl: './bestellliste.component.css'
})

export class BestelllisteComponent  {}
