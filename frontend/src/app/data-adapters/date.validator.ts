import { AbstractControl, ValidatorFn } from '@angular/forms';

export function dateRangeValidator(): ValidatorFn {
    return (formGroup: AbstractControl): { [key: string]: any } | null => {
        const fromDate = formGroup.get('datum')?.value;
        const toDate = formGroup.get('datumBis')?.value;

        if (fromDate && toDate && new Date(fromDate) > new Date(toDate)) {
            return { 'dateRangeInvalid': true };
        }
        return null;
    };
}
