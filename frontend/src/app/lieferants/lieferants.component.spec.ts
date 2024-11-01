import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LieferantsComponent } from './lieferants.component';

describe('LieferantsComponent', () => {
  let component: LieferantsComponent;
  let fixture: ComponentFixture<LieferantsComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [LieferantsComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(LieferantsComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
