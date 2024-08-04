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
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('ZM 32');
        $pdf->SetTitle($dokumentTitle);
        $pdf->SetSubject('Bestellungen');

        $pdf->SetHeaderData('', 0, $dokumentTitle, 'ZM 32', array(0, 64, 255), array(0, 64, 128));
        $pdf->setHeaderFont(array('helvetica', '', 12));
        $pdf->setFooterFont(array('helvetica', '', 10));
        $pdf->SetMargins(15, 30, 15);
        $pdf->SetHeaderMargin(10);
        $pdf->SetFooterMargin(15);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->SetFont('helvetica', '', 12);
        $pdf->AddPage();

        // Initialize content variable
        $html = '';

        /** @var Bestellung $bestellung */
        foreach ($bestellungen as $bestellung) {
            // Add a section title for each Bestellung with visual separation
            $html .= '<h2 style="border-bottom: 2px solid #000; padding-bottom: 5px; margin-bottom: 15px; margin-top: 20px; font-size: 16px;">Bestellung ID: ' . $bestellung->getId() . '</h2>';
            $html .= '<div style="border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 25px; background-color: #f9f9f9;">';

            // Add article details using a table
            $html .= '<table border="0" cellpadding="4" cellspacing="0" width="100%">';
            $html .= '<thead><tr><th style="border-bottom: 1px solid #ddd; background-color: #f2f2f2;">Artikel Details</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($bestellung->getArtikels() as $artikel) {
                $html .= '<tr>';
                $html .= '<td><strong>Artikel:</strong> ' . $artikel->getName() . '<br>';
                $html .= '<strong>Zusatzinfo:</strong> ' . $artikel->getDescription() . '</td>';
                $html .= '</tr>';
            }
            $html .= '</tbody></table>';

            // Add Bestellung details
            $html .= '<table border="0" cellpadding="4" cellspacing="0" width="100%" style="margin-top: 15px;">';
            $html .= '<tbody>';
            $html .= '<tr><td style="width: 30%;"><strong>Notizen:</strong></td><td>' . $bestellung->getDescription() . '</td></tr>';
            $html .= '<tr><td><strong>Datum:</strong></td><td>' . $bestellung->getDatum()->format('d.m.Y') . '</td></tr>';
            $html .= '<tr><td><strong>Status:</strong></td><td>' . BestellungStatus::getStatusString($bestellung->getStatus()) . '</td></tr>';
            $html .= '<tr><td><strong>Preis:</strong></td><td>' . $bestellung->getPreis() . '</td></tr>';
            $html .= '<tr><td><strong>Menge:</strong></td><td>' . $bestellung->getAmount() . '</td></tr>';
            $html .= '<tr><td><strong>Bestellt von:</strong></td><td>' . $bestellung->getMitarbeiter()->getNachname() . ' ' . $bestellung->getMitarbeiter()->getVorname() . '</td></tr>';
            $html .= '</tbody></table>';

            // Add Departments, Lieferants, and Herstellers
            $html .= '<table border="0" cellpadding="4" cellspacing="0" width="100%" style="margin-top: 15px;">';
            $html .= '<thead><tr><th style="border-bottom: 1px solid #ddd; background-color: #f2f2f2;">Weitere Informationen</th></tr></thead>';
            $html .= '<tbody>';
            foreach ($bestellung->getDepartments() as $department) {
                $html .= '<tr><td><strong>Abteilung:</strong> ' . $department->getName() . '</td></tr>';
            }
            foreach ($bestellung->getLieferants() as $lieferant) {
                $html .= '<tr><td><strong>Lieferant:</strong> ' . $lieferant->getName() . '</td></tr>';
                $bestellnummer = $this->getLieferantBestellnummer($bestellung, $lieferant);

                if ($bestellnummer !== null) {
                    $html .= '<tr><td><strong>Lieferant-Bestellnummer:</strong> ' . $bestellnummer . '</td></tr>';
                }
            }
            foreach ($bestellung->getHerstellers() as $hersteller) {
                $html .= '<tr><td><strong>Hersteller:</strong> ' . $hersteller->getName() . '</td></tr>';

                $refnummer = $this->getHerstellerRefnummer($bestellung, $hersteller);

                if ($refnummer !== null) {
                    $html .= '<tr><td><strong>Hersteller-REF-Nummer:</strong> ' . $refnummer . '</td></tr>';
                }
            }
            $html .= '</tbody></table>';

            $html .= '</div>';

            // Add a page break if desired
            // Uncomment the line below if you want each Bestellung on a new page
            // $pdf->AddPage();
        }

        // Output the content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Set HTTP headers for PDF inline display or download
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="example.pdf"');
        header('Content-Transfer-Encoding: binary');
        header('Content-Length: ' . strlen($pdf->Output('', 'S')));

        // Close and output PDF document
        $pdf->Output('example.pdf', 'D');
    }

    private function getLieferantBestellnummer(Bestellung $bestellung, Lieferant $lieferant)
    {
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