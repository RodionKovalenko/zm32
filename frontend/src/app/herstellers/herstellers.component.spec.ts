import { ComponentFixture, TestBed } from '@angular/core/testing';

import { HerstellersComponent } from './herstellers.component';

describe('HerstellersComponent', () => {
  let component: HerstellersComponent;
  let fixture: ComponentFixture<HerstellersComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [HerstellersComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(HerstellersComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
