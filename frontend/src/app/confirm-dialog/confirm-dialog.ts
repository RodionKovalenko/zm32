import { Component, EventEmitter, Output, Input } from '@angular/core';
import { Inject } from '@angular/core';
import { MatDialogRef, MAT_DIALOG_DATA } from '@angular/material/dialog';

@Component({
  selector: 'app-confirm-dialog',
  templateUrl: './confirm-dialog.html',
  styleUrl: './confirm-dialog.css'
})
export class ConfirmDialog {
    constructor(
        public dialogRef: MatDialogRef<ConfirmDialog>,
        @Inject(MAT_DIALOG_DATA) public data: any
    ) {}

    @Output() confirmed = new EventEmitter<string>();
    @Output() cancelled = new EventEmitter<string>();
    @Input() title: string = 'Bestätigung';
    @Input() message: string = 'Möchten Sie fortfahren?';
    @Input() confirmText: string = 'Ja';
    @Input() cancelText: string = 'Abbrechen';

    onConfirm() {
        this.dialogRef.close('confirmed');
    }

    onCancel() {
        this.dialogRef.close('cancelled');
    }
}
