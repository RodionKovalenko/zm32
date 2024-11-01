import { ComponentFixture, TestBed } from '@angular/core/testing';

import { PersonalGridComponent } from './personal-grid.component';

describe('PersonalGridComponent', () => {
  let component: PersonalGridComponent;
  let fixture: ComponentFixture<PersonalGridComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [PersonalGridComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(PersonalGridComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
