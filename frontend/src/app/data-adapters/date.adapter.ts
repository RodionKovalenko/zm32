import { NativeDateAdapter } from '@angular/material/core';
import {Injectable} from "@angular/core";

@Injectable({
  providedIn: 'root',  // You can define the provider scope as 'root' for application-wide availability
})
export class CustomDateAdapter extends NativeDateAdapter {
    // Override the format method
    override format(date: Date, displayFormat: Object): string {
        const day = date.getDate().toString().padStart(2, '0');
        const month = (date.getMonth() + 1).toString().padStart(2, '0'); // Months are 0-based
        const year = date.getFullYear();
        return `${day}.${month}.${year}`;
    }

    // Override the parse method
    override parse(value: any, parseFormat: Object): Date | null {
        if (typeof value === 'string') {
            const [day, month, year] = value.split('.').map(v => parseInt(v, 10));
            return new Date(year, month - 1, day);
        }
        return super.parse(value, parseFormat);
    }
}
