export interface Lieferant {
    id: Number;
    name: string;
    formTitle?: string;
    lieferantStammdaten: [
        {
            id?: Number;
            typ?: Number;
            plz?: string;
            ort?: string;
            adresse?: string;
            telefon?: string;
            email?: string;
            url?: string;
        }
    ]
}
