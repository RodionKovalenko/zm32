import {Artikel} from "./Artikel";
import {Lieferant} from "./Lieferant";
import {Mitarbeiter} from "./Mitarbeiter";

export interface Bestellung {
    id: number;
    description: string;
    descriptionZusatz?: string;
    amount: string;
    preis?: number;
    gesamtpreis?: number;
    packageunit?: string;
    artikel?: Artikel
    lieferant?: Lieferant,
    mitarbeiter?: Mitarbeiter,
    departmentId?: Number,
    formTitle: '',
    lieferants?: any[],
    herstellers?: any[],
    departments?: any[],
    artikels?: any[],
    herstellerStandorte?: any[],
    lieferantStandorte?: any []
}
