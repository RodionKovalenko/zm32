import { MatDateFormats } from '@angular/material/core';

export const MY_DATE_FORMATS: MatDateFormats = {
    parse: {
        dateInput: 'DD.MM.YYYY', // Format for parsing input values
    },
    display: {
        dateInput: 'DD.MM.YYYY', // Format for displaying input values
        monthYearLabel: 'MMM YYYY',
        monthYearA11yLabel: 'MMMM YYYY',
        dateA11yLabel: 'DD MMMM YYYY',
    },
};
