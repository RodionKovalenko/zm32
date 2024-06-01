import { ComponentFixture, TestBed } from '@angular/core/testing';

import { MaterialEditComponentComponent } from './material-edit-component.component';

describe('MaterialEditComponentComponent', () => {
  let component: MaterialEditComponentComponent;
  let fixture: ComponentFixture<MaterialEditComponentComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [MaterialEditComponentComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(MaterialEditComponentComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
