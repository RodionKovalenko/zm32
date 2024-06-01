import {Component, Inject} from '@angular/core';
import {MAT_DIALOG_DATA, MatDialogRef} from "@angular/material/dialog";
import {UserData} from "../data-grid.component";

@Component({
    selector: 'app-material-edit-component',
    templateUrl: './material-edit-component.component.html',

    styleUrl: './material-edit-component.component.css',
})
export class MaterialEditComponentComponent {
    constructor(
        public dialogRef: MatDialogRef<MaterialEditComponentComponent>,
        @Inject(MAT_DIALOG_DATA) public data: UserData
    ) {
    }

    onNoClick(): void {
        this.dialogRef.close();
    }

    save(): void {
        this.dialogRef.close(this.data);
    }
}
