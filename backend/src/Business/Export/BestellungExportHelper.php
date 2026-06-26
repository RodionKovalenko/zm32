<?php

namespace App\Business\Export;

use App\Entity\Bestellung;
use App\Entity\Material\Artikel;
use App\Entity\Material\ArtikelToHerstRefnummer;
use App\Entity\Material\Hersteller;
use App\Entity\Material\Lieferant;

class TcpdfWithPageNumbers extends \TCPDF
{
    public function Footer(): void
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', '', 9);
        $this->Cell(0, 10, 'Seite ' . $this->getAliasNumPage() . ' / ' . $this->getAliasNbPages(), 0, false, 'C');
    }
}

class BestellungExportHelper
{
    public function generateExport($bestellungen)
    {
        $pdf = new TcpdfWithPageNumbers();
        $currentDateString = (new \DateTime())->format('d.m.Y H:i');
        $dokumentTitle = 'Bestellungen ' . $currentDateString;

        $pdf->setCreator(PDF_CREATOR);
        $pdf->setAuthor('ZM 32');
        $pdf->setHeaderData('', 0, $dokumentTitle, 'ZM 32', array(0, 64, 255), array(0, 64, 128));
        $pdf->setHeaderMargin(15);
        $pdf->setFooterMargin(10);

        $pdf->setHeaderFont(array('helvetica', '', 12));
        $pdf->setFooterFont(array('helvetica', '', 9));
        $pdf->setMargins(15, 30, 15);
        $pdf->setAutoPageBreak(true, 20);
        $pdf->setFont('helvetica', '', 10);
        $pdf->addPage();

        // Table column widths
        $fixedWidthColumns = 20 + 15 + 20 + 20 + 20 + 30; // Datum, Menge, Bestellnummer, Preis, Gesamtpreis, Bestellt von
        $totalPageWidth = 210 - 30; // A4 width minus margins
        $remainingWidth = $totalPageWidth - $fixedWidthColumns;
        $artikelWidth = $remainingWidth * 0.6;
        $notizenWidth = $remainingWidth * 0.4;

        $totalSum = 0;
        $bestellungenByLieferant = [];

        // Group orders by supplier
        foreach ($bestellungen as $bestellung) {
            $lieferant = $bestellung->getLieferants()[0] ?? null;
            $lieferantName = $lieferant ? $lieferant->getName() : 'N/A';
            $bestellungenByLieferant[$lieferantName][] = $bestellung;
        }

        // Generate table for each supplier — written individually to prevent orphaned headings
        foreach ($bestellungenByLieferant as $lieferantName => $lieferantBestellungen) {
            // Ensure heading + table header + at least one data row fit on the same page (min. 26mm)
            $availableSpace = $pdf->getPageHeight() - $pdf->GetY() - $pdf->getBreakMargin();
            if ($availableSpace < 26) {
                $pdf->AddPage();
            }

            $supplierSum = 0;

            $html = '<h2 style="font-size: 14px; margin: 5px 0;">Lieferant: ' . $lieferantName . '</h2>';
            $html .= '<table border="1" cellpadding="3" cellspacing="0" width="100%" style="border-collapse: collapse;">';

            $html .= '<thead>
                    <tr>
                        <th width="20mm">Bestellnummer</th>
                        <th width="' . $artikelWidth . 'mm">Artikel</th>
                         <th width="15mm">Menge</th>
                        <th width="20mm">Preis</th>
                        <th width="25mm">Gesamtpreis</th>
                        <th width="' . $notizenWidth . 'mm">Notizen</th>
                        <th width="30mm">Bestellt von</th>
                        <th width="20mm">Datum</th>
                    </tr>
                  </thead>';

            $html .= '<tbody>';

            foreach ($lieferantBestellungen as $bestellung) {
                foreach ($bestellung->getArtikels() as $artikel) {
                    $lieferantBestellnummer = $this->getLieferantBestellnummer($bestellung, $bestellung->getLieferants()[0]);
                    $bestelltVon = $bestellung->getMitarbeiter()->getVorname() . ' ' . $bestellung->getMitarbeiter()->getNachname();
                    $datum = $bestellung->getDatum()->format('d.m.Y');
                    $preis = $bestellung->getPreis() ?: 0;
                    $preis = (float)str_replace(',', '.', $preis);
                    $gesamtpreis = $preis * $this->getAmountNumber($bestellung);
                    $formattedPreis = number_format($preis, 2, ',', '.') . ' €';
                    $formattedGesamtpreis = number_format($gesamtpreis, 2, ',', '.') . ' €';

                    $supplierSum += $gesamtpreis;
                    $totalSum += $gesamtpreis;

                    $html .= '<tr>
                            <td width="20mm">' . ($lieferantBestellnummer ?? 'N/A') . '</td>
                            <td width="' . $artikelWidth . 'mm">' . $artikel->getName() . '</td>
                            <td width="15mm" style="text-align: right;">' . $bestellung->getAmount() . '</td>
                            <td width="20mm" style="text-align: right;">' . $formattedPreis . '</td>
                            <td width="25mm" style="text-align: right;">' . $formattedGesamtpreis . '</td>
                            <td width="' . $notizenWidth . 'mm">' . $bestellung->getDescription() . '</td>
                            <td width="30mm">' . $bestelltVon . '</td>
                            <td width="20mm">' . $datum . '</td>
                        </tr>';
                }
            }

            // Subtotal row for this supplier
            $html .= '<tr style="font-weight: bold; background-color: #f2f2f2;">
                <td colspan="4" style="text-align: right;">Summe für ' . $lieferantName . ':</td>
                <td style="text-align: right;">' . number_format($supplierSum, 2, ',', '.') . ' €</td>
                <td colspan="3"></td>
              </tr>';

            $html .= '</tbody></table><br>';

            $pdf->writeHTML($html, true, false, true, false, '');
        }

        // Final total — start new page if not enough room
        $availableSpace = $pdf->getPageHeight() - $pdf->GetY() - $pdf->getBreakMargin();
        if ($availableSpace < 15) {
            $pdf->AddPage();
        }

        $totalHtml = '<h2 style="text-align: right; margin-top: 15px;">Gesamtsumme: ' . number_format($totalSum, 2, ',', '.') . ' €</h2>';
        $pdf->writeHTML($totalHtml, true, false, true, false, '');

        // Set HTTP headers
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="export_bestellungen.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($pdf->Output('', 'S')));

        // Close and output PDF
        $pdf->Output('export_bestellungen.pdf', 'D');
    }

    private function getLieferantBestellnummer(Bestellung $bestellung, ?Lieferant $lieferant = null)
    {
        if ($lieferant === null) {
            return null;
        }

        foreach ($bestellung->getArtikels() as $artikel) {
            foreach ($artikel->getArtikelToLieferantBestellnummers() as $artikelToLieferantBestellnummer) {
                if ($artikelToLieferantBestellnummer->getLieferant() === $lieferant) {
                    return $artikelToLieferantBestellnummer->getBestellnummer();
                }
            }
        }

        return null;
    }

    private function getHerstellerRefnummer(Bestellung $bestellung, Hersteller $hersteller)
    {
        /** @var Artikel $artikel */
        foreach ($bestellung->getArtikels() as $artikel) {
            /** @var ArtikelToHerstRefnummer $artikelToHerstellerRefnummer */
            foreach ($artikel->getArtikelToHerstRefnummers() as $artikelToHerstellerRefnummer) {
                if ($artikelToHerstellerRefnummer->getHersteller() === $hersteller) {
                    return $artikelToHerstellerRefnummer->getRefnummer();
                }
            }
        }

        return null;
    }

    private function getAmountNumber(Bestellung $bestellung)
    {
        $amountString = $bestellung->getAmount();
        preg_match('/\d+(\.\d+)?/', $amountString, $matches);
        $amount = isset($matches[0]) ? (float)$matches[0] : 1.0;

        return $amount;
    }
}
