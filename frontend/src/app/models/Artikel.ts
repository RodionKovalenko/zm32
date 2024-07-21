
export interface Artikel {
    id: number;
    name: string;
    description?: string;
    formTitle?: string;
    quantity?: number;
    lieferants?: any[];
    departments?: any[];
    herstellers?: any[];
}
