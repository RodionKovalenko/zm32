import { ComponentFixture, TestBed } from '@angular/core/testing';

import { RegistrateUserComponent } from './registrate-user.component';

describe('RegistrateUserComponent', () => {
  let component: RegistrateUserComponent;
  let fixture: ComponentFixture<RegistrateUserComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [RegistrateUserComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(RegistrateUserComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
