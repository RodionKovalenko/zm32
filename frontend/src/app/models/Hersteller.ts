export interface Hersteller {
    id: Number;
    name: string;
    url?: string;
    formTitle?: string;
    standorte: [
        {
            id?: Number;
            plz?: string;
            ort?: string;
            adresse?: string;
            telefon?: string;
            email?: string;
            url?: string;
        }
    ]
}
