import { ComponentFixture, TestBed } from '@angular/core/testing';

import { UploadSuccess } from './upload-success';

describe('UploadSuccess', () => {
  let component: UploadSuccess;
  let fixture: ComponentFixture<UploadSuccess>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [UploadSuccess]
    })
    .compileComponents();

    fixture = TestBed.createComponent(UploadSuccess);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
