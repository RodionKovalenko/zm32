<?php

namespace App\Business\Export;

use App\Entity\Bestellung;
use App\Entity\BestellungStatus;
use App\Entity\Material\Artikel;
use App\Entity\Material\ArtikelToHerstRefnummer;
use App\Entity\Material\ArtikelToLieferBestellnummer;
use App\Entity\Material\Hersteller;
use App\Entity\Material\Lieferant;

class BestellungExportHelper
{
    public function generateExport($bestellungen)
    {
        // Create new PDF document
        $pdf = new \TCPDF();
        $currentDateString = (new \DateTime())->format('d.m.Y H:i');
        $dokumentTitle = 'Export Bestellungen ' . $currentDateString;

        // Set document information
        $pdf->setCreator(PDF_CREATOR);
        $pdf->setAuthor('ZM 32');
        $pdf->setTitle($dokumentTitle);
        $pdf->setSubject('Bestellungen');

        $pdf->setHeaderData('', 0, $dokumentTitle, 'ZM 32', array(0, 64, 255), array(0, 64, 128));
        $pdf->setHeaderFont(array('helvetica', '', 12));
        $pdf->setFooterFont(array('helvetica', '', 10));
        $pdf->setMargins(15, 30, 15);
        $pdf->setHeaderMargin(10);
        $pdf->setFooterMargin(15);
        $pdf->setAutoPageBreak(true, 20);
        $pdf->setFont('helvetica', '', 10);
        $pdf->addPage();

        // Initialize content variable
        $html = '<h1 style="margin-bottom: 5px;">' . $dokumentTitle . '</h1>';

        // Group Bestellungen by Lieferants
        $bestellungenByLieferant = [];

        foreach ($bestellungen as $bestellung) {
            /** @var Lieferant $lieferant */
            $lieferant = $bestellung->getLieferants()[0] ?? null;

            if ($lieferant !== null) {
                $bestellungenByLieferant[$lieferant->getName()][] = $bestellung;
            } else {
                $bestellungenByLieferant['N/A'][] = $bestellung;
            }
        }

        // Loop through each Lieferant group
        foreach ($bestellungenByLieferant as $lieferantName => $bestellungen) {
            // Add Lieferant header with smaller font and reduced margin
            $html .= '<h2 style="font-size: 14px; margin: 5px 0;">Lieferant: ' . $lieferantName . '</h2>';

            // Start the table for this Lieferant group with smaller cell padding
            $html .= '<table border="1" cellpadding="3" cellspacing="0" width="100%" style="border-collapse: collapse;">';

            // Add table header (only once per Lieferant)
            $html .= '<thead>
                    <tr>
                        <th width="30mm" style="border: 1px solid #000; background-color: #f2f2f2; text-align: left;">Menge</th>
                        <th width="45mm" style="border: 1px solid #000; background-color: #f2f2f2; text-align: left;">Bestellnummer</th>
                        <th width="45mm" style="border: 1px solid #000; background-color: #f2f2f2; text-align: left;">Artikel</th>
                        <th width="30mm" style="border: 1px solid #000; background-color: #f2f2f2; text-align: left;">Notizen</th>
                    </tr>
                  </thead>';

            $html .= '<tbody>';

            // Data rows for each Bestellung in the current Lieferant group
            foreach ($bestellungen as $bestellung) {
                /** @var Artikel $artikel */
                foreach ($bestellung->getArtikels() as $artikel) {
                    $lieferantBestellnummer = $this->getLieferantBestellnummer($bestellung, $bestellung->getLieferants()[0]);

                    $html .= '<tr>';
                    $html .= '<td width="30mm" style="border: 1px solid #000; vertical-align: top; padding: 3px;">' . $bestellung->getAmount() . '</td>';
                    $html .= '<td width="45mm" style="border: 1px solid #000; vertical-align: top; padding: 3px;">' . ($lieferantBestellnummer ?? 'N/A') . '</td>';
                    $html .= '<td width="45mm" style="border: 1px solid #000; vertical-align: top; padding: 3px;">' . $artikel->getName() . '</td>';
                    $html .= '<td width="30mm" style="border: 1px solid #000; vertical-align: top; padding: 3px;">' . $bestellung->getDescription() . '</td>';
                    $html .= '</tr>';
                }
            }

            $html .= '</tbody></table>';  // Close the table for this Lieferant group
        }

        // Output the content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Set HTTP headers for PDF inline display or download
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="export_bestellungen.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($pdf->Output('', 'S')));

        // Close and output PDF document
        $pdf->Output('export_bestellungen.pdf', 'D');
    }

    private function getLieferantBestellnummer(Bestellung $bestellung, ?Lieferant $lieferant = null)
    {
        if ($lieferant === null) {
            return null;
        }
        /** @var Artikel $artikel */
        foreach ($bestellung->getArtikels() as $artikel) {
            /** @var ArtikelToLieferBestellnummer $artikelToLieferantBestellnummer */
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
}