import { Component } from '@angular/core';
import {PersonalGridComponent} from "./personal-grid/personal-grid.component";

@Component({
  selector: 'app-personal',
  templateUrl: './personal.component.html',
  imports: [
    PersonalGridComponent
  ],
  styleUrl: './personal.component.css'
})
export class PersonalComponent {

}
