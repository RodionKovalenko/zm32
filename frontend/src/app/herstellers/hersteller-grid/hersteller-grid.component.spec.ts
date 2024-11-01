import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HerstellerGridComponent } from './hersteller-grid.component';

describe('HerstellerGridComponent', () => {
  let component: HerstellerGridComponent;
  let fixture: ComponentFixture<HerstellerGridComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HerstellerGridComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(HerstellerGridComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
