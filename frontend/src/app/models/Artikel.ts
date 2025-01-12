
export interface Artikel {
    id: number;
    name: string;
    description?: string;
    preis?: number;
    formTitle?: string;
    quantity?: number;
    lieferants?: any[];
    departments?: any[];
    herstellers?: any[];
    url?: string;
    artikelToHerstRefnummers?: any[];
    artikelToLieferantBestellnummers?: any[];
}
