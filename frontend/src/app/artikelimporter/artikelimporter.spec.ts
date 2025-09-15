import { ComponentFixture, TestBed } from '@angular/core/testing';

import { Artikelimporter } from './artikelimporter';

describe('Artikelimporter', () => {
  let component: Artikelimporter;
  let fixture: ComponentFixture<Artikelimporter>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [Artikelimporter]
    })
    .compileComponents();

    fixture = TestBed.createComponent(Artikelimporter);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
