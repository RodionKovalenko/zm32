import { ComponentFixture, TestBed } from '@angular/core/testing';

import { LieferantGridComponent } from './lieferant-grid.component';

describe('LieferantGridComponent', () => {
  let component: LieferantGridComponent;
  let fixture: ComponentFixture<LieferantGridComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [LieferantGridComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(LieferantGridComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
