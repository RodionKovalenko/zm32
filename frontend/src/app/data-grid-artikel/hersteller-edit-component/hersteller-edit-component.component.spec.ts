import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HerstellerEditComponentComponent } from './hersteller-edit-component.component';

describe('HerstellerEditComponentComponent', () => {
  let component: HerstellerEditComponentComponent;
  let fixture: ComponentFixture<HerstellerEditComponentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HerstellerEditComponentComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(HerstellerEditComponentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
