import {Component} from '@angular/core';
import {DepartmentGridComponent} from "./department-grid/department-grid.component";
import {MatFormFieldModule} from "@angular/material/form-field";
import {MatInputModule} from "@angular/material/input";

@Component({
  selector: 'app-departments',
  templateUrl: './departments.component.html',
  imports: [
    DepartmentGridComponent,
    MatFormFieldModule,
    MatInputModule,
  ],
  styleUrl: './departments.component.css'
})
export class DepartmentsComponent {
}
