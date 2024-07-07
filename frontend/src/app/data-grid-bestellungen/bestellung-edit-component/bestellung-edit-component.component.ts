import {Component, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from "@angular/material/dialog";
import {MaterialData} from "../../models/Material";

@Component({
  selector: 'app-bestellung-edit-component',
  templateUrl: './bestellung-edit-component.component.html',
  styleUrl: './bestellung-edit-component.component.css'
})
export class BestellungEditComponentComponent {
  constructor(
      public dialogRef: MatDialogRef<BestellungEditComponentComponent>,
      @Inject(MAT_DIALOG_DATA) public data: MaterialData
  ) {
  }

  onNoClick(): void {
    this.dialogRef.close();
  }

  save(): void {
    this.dialogRef.close(this.data);
  }
}
