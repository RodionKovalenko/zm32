import {Directive, ElementRef, AfterViewInit} from '@angular/core';

@Directive({
    selector: '[appFocusInput]', // Consider renaming to appAutoFocus for clarity
})
export class FocusInputDirective implements AfterViewInit {
    constructor(private elementRef: ElementRef) {
    }

    ngAfterViewInit(): void {
        setTimeout(() => {
            // Directly focus the element if it's an input
            if (this.elementRef.nativeElement.tagName === 'INPUT') {
                this.elementRef.nativeElement.focus();
            } else {
                // If it's a wrapper, try to find an input inside
                const input = this.elementRef.nativeElement.querySelector('input');
                if (input) {
                    input.focus();
                }
            }
        }, 500);
    }
}