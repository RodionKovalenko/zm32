import { ComponentFixture, TestBed } from '@angular/core/testing';

import { BestelllisteComponent } from './bestellliste.component';

describe('BestelllisteComponent', () => {
  let component: BestelllisteComponent;
  let fixture: ComponentFixture<BestelllisteComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [BestelllisteComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(BestelllisteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
