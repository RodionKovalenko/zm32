import {LieferantToArtikel} from "./LieferantToArtikel";

export interface Artikel {
    id: number;
    name: string;
    quantity: number;
    description: string;
    lieferantToArtikels?: LieferantToArtikel[];
    formTitle?: string;
}
