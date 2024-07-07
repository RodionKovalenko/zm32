import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DataGridArtikelComponent } from './data-grid-artikel.component';

describe('DataGridArtikelComponent', () => {
  let component: DataGridArtikelComponent;
  let fixture: ComponentFixture<DataGridArtikelComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DataGridArtikelComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(DataGridArtikelComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
