export function parsePreis(value: string | number | null | undefined): number {
    if (value === null || value === undefined || value === '') return 0;
    return parseFloat(value.toString().replace(',', '.')) || 0;
}

export function formatPreisDE(value: string | number | null | undefined): string {
    if (value === null || value === undefined || value === '') return '';
    const num = parsePreis(value);
    if (isNaN(num)) return '';
    return num.toLocaleString('de-DE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
