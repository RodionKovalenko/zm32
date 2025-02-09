import { Directive, ElementRef, HostListener } from '@angular/core';

@Directive({
  selector: '[appFocusOnClick]',
})
export class FocusOnClickDirective {
  constructor(private elementRef: ElementRef) {}

  @HostListener('click', ['$event.target'])
  onClick(btn: any) {
    // console.log('button', btn, 'number of clicks:');
    const input = this.elementRef.nativeElement.querySelector(
      '.filter-textbox > input'
    );
    if (input) {
      input.focus();
    }
  }
}
