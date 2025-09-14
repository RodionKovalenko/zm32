<?php

namespace App\Business\Export;

use App\Entity\Material\Artikel;
use App\Entity\Material\ArtikelToHerstRefnummer;
use App\Entity\Material\ArtikelToLieferBestellnummer;
use App\Entity\Material\Hersteller;
use App\Entity\Material\Lieferant;
use App\Repository\Material\ArtikelRepository;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ArtikelExporter
{
    public function __construct(private readonly ArtikelRepository $artikelRepository)
    {
    }

    public function generateArtikelExcelExport(array $params = []): string
    {
        // generate and return an Excel file with entity Artikel
        $params['withoutLimit'] = true;
        $artikels = $this->artikelRepository->getByParams($params);

        // Logic to create and return an Excel file goes here

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // set headers
        $headers = ['Artikelname', 'Zusatzinfo', 'Webseite/URL', 'Verpackungseinheit', 'Preis', 'Abteilung', 'Hersteller', 'Hersteller REF-Nummer', 'Lieferant', 'Lieferant Bestellnummer', 'Id'];
        $columnIndex = 2;
        $rowIndex = 1;
        foreach ($headers as $header) {
            $sheet->setCellValueExplicit([$columnIndex, $rowIndex], $header, DataType::TYPE_STRING);
            $sheet->getColumnDimensionByColumn($columnIndex)->setWidth(20);
            $sheet->getStyle([$columnIndex, $rowIndex])->getFont()->setBold(true);
            $columnIndex++;
        }

        $sheet->freezePane('A2');

        $rowIndex = 2;
        /** @var Artikel $artikel */
        foreach ($artikels as $artikel) {
            $columnIndex = 2;
            $columnIndexStart = $columnIndex;
            $sheet->setCellValueExplicit([$columnIndex++, $rowIndex], $artikel->getName(), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([$columnIndex++, $rowIndex], $artikel->getDescription(), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([$columnIndex++, $rowIndex], $artikel->getUrl(), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([$columnIndex++, $rowIndex], $artikel->getPackageunit(), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([$columnIndex++, $rowIndex], $this->normalizeDecimal($artikel->getPreis()), DataType::TYPE_NUMERIC);

            $departmentNames = '';
            foreach ($artikel->getDepartments() as $department) {
                $departmentNames .= $department->getName() . ', ';
            }
            $sheet->setCellValueExplicit([$columnIndex++, $rowIndex], rtrim($departmentNames, ', '), DataType::TYPE_STRING);

            $herstellerNames = '';
            /** @var Hersteller $hersteller */
            foreach ($artikel->getHerstellers() as $hersteller) {
                $herstellerNames .= $hersteller->getName() . ', ';
            }
            $sheet->setCellValueExplicit([$columnIndex++, $rowIndex], rtrim($herstellerNames, ', '), DataType::TYPE_STRING);

            $artikelRefNummern = '';
            /** @var ArtikelToHerstRefnummer $ref */
            foreach ($artikel->getArtikelToHerstRefnummers() as $ref) {
                $herstellerName = $ref->getHersteller()?->getName();
                $artikelRefNummern .= $ref->getRefnummer() . ' (' . $herstellerName . '), ';
            }
            $sheet->setCellValueExplicit([$columnIndex++, $rowIndex], rtrim($artikelRefNummern, ', '), DataType::TYPE_STRING);

            $lieferanterNames = '';
            /** @var Lieferant $lieferant */
            foreach ($artikel->getLieferants() as $lieferant) {
                $lieferanterNames .= $lieferant->getName() . ', ';
            }
            $sheet->setCellValueExplicit([$columnIndex++, $rowIndex], rtrim($lieferanterNames, ', '), DataType::TYPE_STRING);

            $artikelBestellnummern = '';
            /** @var ArtikelToLieferBestellnummer $bestellnummer */
            foreach ($artikel->getArtikelToLieferantBestellnummers() as $bestellnummer) {
                $lieferantName = $bestellnummer->getLieferant()?->getName();
                $artikelBestellnummern = $bestellnummer->getBestellnummer() . ' (' . $lieferantName . ') ';
            }
            $sheet->setCellValueExplicit([$columnIndex++, $rowIndex], rtrim($artikelBestellnummern, ', '), DataType::TYPE_STRING);
            $sheet->setCellValueExplicit([$columnIndex++, $rowIndex], $artikel->getId(), DataType::TYPE_NUMERIC);

            $sheet->getStyle([$columnIndexStart, $rowIndex, $columnIndex, $rowIndex])->getAlignment()->setWrapText(true);
            // set alignment to the top left of the cell
            $sheet->getStyle([$columnIndexStart, $rowIndex, $columnIndex, $rowIndex])->getAlignment()->setVertical('top')->setHorizontal('left');
            $rowIndex++;
        }

        $writer = new Xlsx($spreadsheet);

        // Schreibe in einen Speicher-Stream
        $stream = fopen('php://memory', 'r+');
        $writer->save($stream);
        rewind($stream);
        $excelContent = stream_get_contents($stream);
        fclose($stream);

        // Kodieren als Base64
        return base64_encode($excelContent);
    }

    private function normalizeDecimal($value) {
        if (is_string($value)) {
            // Remove spaces (if any)
            $num = str_replace(' ', '', $value);
            // If the decimal separator is a comma and dot is present as thousand sep
            if (strpos($num, ',') !== false && strpos($num, '.') !== false) {
                $num = str_replace('.', '', $num); // remove thousand separator
                $num = str_replace(',', '.', $num); // convert decimal separator
            }
            // If only comma is present (e.g. "123,02"), treat it as decimal sep
            elseif (strpos($num, ',') !== false) {
                $num = str_replace(',', '.', $num);
            }
            return floatval($num);
        }
        return floatval($value);
    }
}