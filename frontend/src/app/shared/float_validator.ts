import { AbstractControl, ValidationErrors } from '@angular/forms';

export function floatValidator(control: AbstractControl): ValidationErrors | null {
  const value = control.value;
  const floatRegex = /^[+-]?\d+(\.\d+)?(,\d+)?$/;
  if (value && !floatRegex.test(value)) {
    return { invalidFloat: true };  // Return custom error key if validation fails
  }
  return null;  // Return null if validation passes
}
