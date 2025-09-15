<?php

namespace App\Business\Import;

use App\Entity\Material\Artikel;
use App\Entity\Material\ArtikelToHerstRefnummer;
use App\Entity\Material\ArtikelToLieferBestellnummer;
use App\Entity\Material\Hersteller;
use App\Entity\Material\Lieferant;
use App\Exception\PublicException;
use App\Repository\DepartmentRepository;
use App\Repository\Material\ArtikelRepository;
use App\Repository\Material\HerstellerRepository;
use App\Repository\Material\LieferantRepository;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ArtikelImporter
{
    public function __construct(
        private readonly ArtikelRepository $artikelRepository,
        private readonly DepartmentRepository $departmentRepository,
        private readonly HerstellerRepository $herstellerRepository,
        private readonly LieferantRepository $lieferantRepository,
    ) {
    }

    public function importExcel($file): int
    {
        $allowedMimeTypes = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv',
        ];
        if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new PublicException('File is not a CSV file');
        }

        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet();

        $artikelRows = [];

        foreach ($sheet->getRowIterator() as $row) {
            $rowIndex = $row->getRowIndex();
            if ($rowIndex < 2) {
                continue; // skip header row 1
            }
            $rowValues = [];
            // Iterate only columns 2 to 12 (B to L)
            $cellIterator = $row->getCellIterator('B', 'L');
            $cellIterator->setIterateOnlyExistingCells(false);

            foreach ($cellIterator as $cell) {
                $rowValues[] = $cell->getFormattedValue();
            }
            $artikelRows[] = $rowValues;
        }

        $artikelsToSave = [];
        foreach ($artikelRows as $artikelData) {
            $artikelName = $artikelData[0];
            $artikelDescription = $artikelData[1] ?? null;
            $artikelUrl = $artikelData[2] ?? null;
            $artikelVerpackungseinheit = $artikelData[3] ?? null;
            $artikelPrice = $artikelData[4] ?? null;
            $abteilungName = $artikelData[5] ?? null;
            $herstellerName = $artikelData[6] ?? null;
            $herstellerRefNummer = $artikelData[7] ?? null;
            $lieferantenNames = $artikelData[8] ?? null;
            $lieferantBestellNummers = $artikelData[9] ?? null;
            $artikelId = $artikelData[10] ?? null;

            if (empty($artikelName)) {
                continue;
            }

            $artikel = null;
            if (!empty($artikelId)) {
                $artikel = $this->artikelRepository->findOneBy(['id' => $artikelId]);
            }

            if ($artikel === null) {
                $artikel = new Artikel();
            }

            $artikel->setName($artikelName);
            $artikel->setDescription($artikelDescription);
            $artikel->setUrl($artikelUrl);
            $artikel->setPackageunit($artikelVerpackungseinheit);
            $artikel->setPreis($artikelPrice);

            if ($artikel->getId() === null) {
                $this->artikelRepository->save($artikel);
            }

            if (!empty($abteilungName)) {
                // split by comma and trim spaces
                $abteilungNames = array_map('trim', explode(',', $abteilungName));

                // find by names case insensitive
                $departments = $this->departmentRepository->findByNamesCaseInsenstitive($abteilungNames);

                foreach ($departments as $department) {
                    $artikel->addDepartment($department);
                }
            }

            $herstellerRefNummers = [];
            if (!empty($herstellerRefNummer)) {
                $herstellerRefNumbers = $this->splitOutsideParentheses($herstellerRefNummer);

                /** @var string $refNumber */
                foreach ($herstellerRefNumbers as $refNumber) {
                    $herstellerRefNummers[] = preg_replace('/\s*\(.*?\)\s*/', '', $refNumber);
                }
            }

            if (!empty($herstellerName)) {
                $herstellerNames = $this->splitOutsideParentheses($herstellerName);

                foreach ($herstellerNames as $index => $herstellerName) {
                    $herstellers = $this->herstellerRepository->findByNamesCaseInsenstitive([$herstellerName]);

                    if (empty($herstellers)) {
                        $herstellers = [new Hersteller()];
                    }

                    foreach ($herstellers as $hersteller) {
                        $artikel->addHersteller($hersteller);
                        $hersteller->setName($herstellerName);

                        if ($hersteller->getId() === null) {
                            $this->herstellerRepository->save($hersteller);
                        }

                        if (!empty($herstellerRefNummers) && array_key_exists($index, $herstellerRefNummers)) {
                            $herstellerRefNummer = $herstellerRefNummers[$index];

                            $istHerstellerExists = false;
                            /** @var ArtikelToHerstRefnummer $artikelToHerstRefnummer */
                            foreach ($artikel->getArtikelToHerstRefnummers() as $artikelToHerstRefnummer) {
                                if ($artikelToHerstRefnummer->getHersteller()->getId() === $hersteller->getId()) {
                                    $artikelToHerstRefnummer->setRefnummer($herstellerRefNummer);
                                    $istHerstellerExists = true;
                                }
                            }

                            if (!$istHerstellerExists) {
                                $artikelToHerstRefnummer = new ArtikelToHerstRefnummer();
                                $artikelToHerstRefnummer->setArtikel($artikel);
                                $artikelToHerstRefnummer->setHersteller($hersteller);
                                $artikelToHerstRefnummer->setRefnummer($herstellerRefNummer);
                                $artikel->addArtikelToHerstRefnummer($artikelToHerstRefnummer);
                            }
                        }
                    }
                }

                $lieferantBestellNumbers = [];
                if (!empty($lieferantBestellNummers)) {
                    $lieferantBestellNummerArray = $this->splitOutsideParentheses($lieferantBestellNummers);

                    /** @var string $lieferantBestellNummer */
                    foreach ($lieferantBestellNummerArray as $lieferantBestellNummer) {
                        $lieferantBestellNumbers[] = preg_replace('/\s*\(.*?\)\s*/', '', $lieferantBestellNummer);
                    }
                }

                if (!empty($lieferantenNames)) {
                    $lieferantNames = $this->splitOutsideParentheses($lieferantenNames);

                    foreach ($lieferantNames as $index => $lieferantName) {
                        $lieferants = $this->lieferantRepository->findByNamesCaseInsenstitive([$lieferantName]);

                        if (empty($lieferants)) {
                            $lieferants = [new Lieferant()];
                        }

                        foreach ($lieferants as $lieferant) {
                            $artikel->addLieferant($lieferant);
                            $lieferant->setName($lieferantName);

                            if ($lieferant->getId() === null) {
                                $this->lieferantRepository->save($lieferant);
                            }

                            if (!empty($lieferantBestellNumbers) && array_key_exists($index, $lieferantBestellNumbers)) {
                                $bestellNummer = $lieferantBestellNumbers[$index];

                                $istLiefarantExists = false;
                                /** @var ArtikelToLieferBestellnummer $lieferToBestellnummer */
                                foreach ($artikel->getArtikelToLieferantBestellnummers() as $lieferToBestellnummer) {
                                    if ($lieferToBestellnummer->getLieferant()->getId() === $lieferant->getId()) {
                                        $lieferToBestellnummer->setBestellnummer($bestellNummer);
                                        $istLiefarantExists = true;
                                    }
                                }

                                if (!$istLiefarantExists) {
                                    $artikelToLieferBestellnummer = new ArtikelToLieferBestellnummer();
                                    $artikelToLieferBestellnummer->setArtikel($artikel);
                                    $artikelToLieferBestellnummer->setLieferant($lieferant);
                                    $artikelToLieferBestellnummer->setBestellnummer($bestellNummer);
                                    $artikel->addArtikelToLieferantBestellnummer($artikelToLieferBestellnummer);
                                }
                            }
                        }
                    }
                }
            }

            $artikelsToSave[] = $artikel;
        }

        $this->artikelRepository->saveAll($artikelsToSave);

        return count($artikelsToSave);
    }

    private function splitOutsideParentheses($input): array
    {
        $result = [];
        $buffer = '';
        $level = 0;

        if (empty($input)) {
            return $result;
        }

        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];

            if ($char == '(') {
                $level++;
            } elseif ($char == ')') {
                if ($level > 0) {
                    $level--;
                }
            }

            if ($char == ',' && $level == 0) {
                // comma outside parentheses - split here
                $result[] = trim($buffer);
                $buffer = '';
            } else {
                $buffer .= $char;
            }
        }
        if ($buffer !== '') {
            $result[] = trim($buffer);
        }

        return $result;
    }
}