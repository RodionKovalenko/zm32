import { ComponentFixture, TestBed } from '@angular/core/testing';

import { DataGridBestellungenComponent } from './data-grid-bestellungen.component';

describe('DataGridBestellungenComponent', () => {
  let component: DataGridBestellungenComponent;
  let fixture: ComponentFixture<DataGridBestellungenComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [DataGridBestellungenComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(DataGridBestellungenComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
