import {Lieferant} from "./Lieferant";
import {Artikel} from "./Artikel";

export interface LieferantToArtikel {
    id: number;
    lieferant: Lieferant;
    artikel: Artikel;
}