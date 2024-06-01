import { Component } from '@angular/core';

@Component({
  selector: 'app-artikelliste',
  templateUrl: './artikelliste.component.html',
  styleUrl: './artikelliste.component.css'
})
export class ArtikellisteComponent {
    items: string[] = ['Arbeitsvorbereitung', 'Edelmetall', 'Kunststoff', 'CadCam'];
    selectedValue: string = 'Arbeitsvorbereitung';
}
