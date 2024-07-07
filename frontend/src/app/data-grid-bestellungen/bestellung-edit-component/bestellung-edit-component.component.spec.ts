import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BestellungEditComponentComponent } from './bestellung-edit-component.component';

describe('BestellungEditComponentComponent', () => {
  let component: BestellungEditComponentComponent;
  let fixture: ComponentFixture<BestellungEditComponentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [BestellungEditComponentComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(BestellungEditComponentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
