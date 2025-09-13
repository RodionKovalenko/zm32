import {Component, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogActions, MatDialogClose, MatDialogContent, MatDialogTitle} from '@angular/material/dialog';
import {MatButton} from "@angular/material/button";

@Component({
  selector: 'app-upload-success',
    imports: [
        MatDialogContent,
        MatDialogActions,
        MatDialogTitle,
        MatButton,
        MatDialogClose
    ],
  templateUrl: './upload-success.html',
  styleUrl: './upload-success.css'
})
export class UploadSuccessComponent {
    constructor(@Inject(MAT_DIALOG_DATA) public data: { title: string, message: string }) {}
}
