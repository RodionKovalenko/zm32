import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LieferantEditComponentComponent } from './lieferant-edit-component.component';

describe('LieferantEditComponentComponent', () => {
  let component: LieferantEditComponentComponent;
  let fixture: ComponentFixture<LieferantEditComponentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [LieferantEditComponentComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(LieferantEditComponentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
