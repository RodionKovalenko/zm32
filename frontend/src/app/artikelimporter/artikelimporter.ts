import {Component} from '@angular/core';
import {MatFormField, MatFormFieldModule} from "@angular/material/form-field";
import {FileInputDirective, FileInputValidators} from '@ngx-dropzone/cdk';
import {MatDropzone} from '@ngx-dropzone/material';
import {HttpService} from "../services/http.service";
import {FormControl, ReactiveFormsModule} from "@angular/forms";
import {MatButton} from "@angular/material/button";
import {LoginErrorComponent} from "../login/login-error/login-error.component";
import {MatDialog} from "@angular/material/dialog";
import {HttpErrorResponse, HttpEvent, HttpEventType, HttpResponse, HttpUploadProgressEvent} from "@angular/common/http";
import {UploadSuccessComponent} from "../upload-success/upload-success";
import {FaIconComponent} from "@fortawesome/angular-fontawesome";
import {faUpload} from "@fortawesome/free-solid-svg-icons";

@Component({
    selector: 'app-artikelimporter',
    imports: [
        MatDropzone,
        MatFormFieldModule,
        MatFormField,
        FileInputDirective,
        ReactiveFormsModule,
        MatButton,
        FaIconComponent,
    ],
    templateUrl: './artikelimporter.html',
    styleUrl: './artikelimporter.css'
})
export class Artikelimporter {
    validators = [FileInputValidators.accept(".xls, .xlsx")];
    profilefile = new FormControl<File | null>(null, this.validators);
    uploadProgress: number = 0;
    isUploading: boolean = false;

    constructor(private http: HttpService, public dialog: MatDialog) {
    }

    uploadFiles() {
        let url = this.http.get_baseUrl() + '/artikel/import?XDEBUG_SESSION_START=ddev';
        let file: File | null = this.profilefile.value;

        const formData = new FormData();

        this.isUploading = true;

        if (file) {
            formData.append('files', file, file.name);
        } else {
            console.error('No file selected to upload');
            return; // stop upload or handle appropriately
        }

        this.http.get_httpclient().post(url, formData, {
            reportProgress: true,
            observe: 'events'
        }).subscribe({
            next: (event: HttpEvent<any>) => {
                switch (event.type) {
                    case HttpEventType.Sent:
                        console.log('Request sent');
                        break;

                    case HttpEventType.UploadProgress:
                        // event is HttpUploadProgressEvent here
                        const progressEvent = event as HttpUploadProgressEvent;
                        if (progressEvent.total) {
                            this.uploadProgress = Math.round(100 * progressEvent.loaded / progressEvent.total);
                            console.log(`Upload Progress: ${this.uploadProgress}%`);
                        }
                        break;

                    case HttpEventType.Response:
                        // event is HttpResponse here
                        const resp = event as HttpResponse<any>;
                        if (resp.body?.success) {
                            console.log('Upload succeeded', resp.body);
                            let numberSaved = resp.body?.data;

                            this.dialog.open(UploadSuccessComponent, {
                                width: '450px',
                                height: '250px',
                                data: { title: 'Upload erfolgreich!', message: 'Die Datei wurde erfolgreich hochgeladen.' + (numberSaved ? ('Es wurden ' + numberSaved + ' Artikel gespeichert.') : '') }
                            });
                        } else {
                            this.dialog.open(LoginErrorComponent, {
                                width: '450px',
                                height: '150px',
                                data: { title: resp.body?.message || 'Upload fehlgeschlagen.' }
                            });
                        }
                        this.isUploading = false;
                        break;
                }
            },
            error: (err: HttpErrorResponse) => {
                console.error('Upload error', err);
                this.dialog.open(LoginErrorComponent, {
                    width: '450px',
                    height: '150px',
                    data: {
                        title: err.error?.message || 'Ein Fehler ist aufgetreten. Bitte versuchen Sie es erneut.'
                    }
                });
            }
        });
    }

    protected readonly faUpload = faUpload;
}