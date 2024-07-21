import {Artikel} from "./Artikel";
import {Lieferant} from "./Lieferant";
import {Mitarbeiter} from "./Mitarbeiter";

export interface Bestellung {
    id: number;
    amount: string;
    description: string;
    descriptionZusatz?: string;
    preis?: number;
    artikel?: Artikel
    lieferant?: Lieferant,
    mitarbeiter?: Mitarbeiter,
    departmentId?: Number,
    formTitle: '',
    artikels?: any[],
    lieferants?: any[],
    herstellers?: any[],
    departments?: any[],
    herstellerStandorte?: any[],
    lieferantStandorte?: any []
}
