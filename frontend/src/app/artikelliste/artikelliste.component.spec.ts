import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ArtikellisteComponent } from './artikelliste.component';

describe('ArtikellisteComponent', () => {
  let component: ArtikellisteComponent;
  let fixture: ComponentFixture<ArtikellisteComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [ArtikellisteComponent]
    })
    .compileComponents();
    
    fixture = TestBed.createComponent(ArtikellisteComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
