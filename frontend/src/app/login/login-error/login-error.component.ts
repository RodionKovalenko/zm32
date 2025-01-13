import {Component, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from "@angular/material/dialog";
import {MatButton} from "@angular/material/button";


@Component({
  selector: 'app-login-error',
  templateUrl: './login-error.component.html',
  imports: [
    MatButton
  ],
  styleUrl: './login-error.component.css'
})
export class LoginErrorComponent  {
    title: string = 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.';

    constructor(
        public dialogRef: MatDialogRef<LoginErrorComponent>,
        @Inject(MAT_DIALOG_DATA) public data: any
    ) {}

    onNoClick(): void {
        this.dialogRef.close();
    }
}
