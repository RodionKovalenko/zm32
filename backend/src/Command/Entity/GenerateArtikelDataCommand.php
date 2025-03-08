<?php

namespace App\Command\Entity;

use App\Entity\Department;
use App\Entity\DepartmentTyp;
use App\Entity\Material\Artikel;
use App\Entity\Material\ArtikelToHerstRefnummer;
use App\Entity\Material\ArtikelToLieferBestellnummer;
use App\Entity\Material\Hersteller;
use App\Entity\Material\Lieferant;
use App\Entity\Mitarbeiter;
use App\Entity\User;
use App\Repository\DepartmentRepository;
use App\Repository\Material\ArtikelRepository;
use App\Repository\Material\ArtikelToHerstellerRefnummerRepository;
use App\Repository\Material\ArtikelToLiefBestellnummerRepository;
use App\Repository\Material\HerstellerRepository;
use App\Repository\Material\LieferantRepository;
use App\Repository\MitarbeiterRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateArtikelDataCommand extends Command
{
    protected static $defaultName = 'zm:generate-default-artikels';
    protected static $defaultDescription = 'Erstellt die default Artikel.';

    public function __construct(
        private readonly ArtikelRepository $artikelRepository,
        private readonly DepartmentRepository $departmentRepository,
        private readonly UserRepository $userRepository,
        private readonly MitarbeiterRepository $mitarbeiterRepository,
        private readonly HerstellerRepository $herstellerRepository,
        private readonly LieferantRepository $lieferantRepository,
        private readonly ArtikelToHerstellerRefnummerRepository $artikelToHerstellerRefnummerRepository,
        private readonly ArtikelToLiefBestellnummerRepository $artikelToLieferantBestellnummerRepository
    ) {
        parent::__construct();
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function configure()
    {
        $this->addOption('force-rewrite', null, null, 'Wenn gesetzt werden die alten Werte geloescht und neu angelegt.')
            ->setDescription(self::$defaultDescription);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $forceRewrite = $input->getOption('force-rewrite');
        $io = new SymfonyStyle($input, $output);

        if ($forceRewrite) {
            $existingArtikel = $this->artikelRepository->findAll();
            $this->artikelRepository->removeAll($existingArtikel);

            $existingMitarbeiter = $this->mitarbeiterRepository->findAll();
            $this->mitarbeiterRepository->removeAll($existingMitarbeiter);

            $existingUser = $this->userRepository->findAll();
            $this->userRepository->removeAll($existingUser);

            $existingDepartment = $this->departmentRepository->findAll();
            $this->departmentRepository->removeAll($existingDepartment);
        }

        $deparmentAlls = $this->departmentRepository->findOneBy(['typ' => DepartmentTyp::ALLE->value]);

        if ($deparmentAlls === null) {
            $deparmentAlls = new Department();
            $deparmentAlls->setName('Alle')
                ->setTyp(DepartmentTyp::ALLE->value);

            $this->departmentRepository->save($deparmentAlls);
        }

        $departmentAllgemein = $this->departmentRepository->findOneBy(['typ' => DepartmentTyp::ALLGEMEIN->value]);

        if ($departmentAllgemein === null) {
            $departmentAllgemein = new Department();
            $departmentAllgemein->setName('Allgemein')
                ->setTyp(DepartmentTyp::ALLGEMEIN->value);

            $this->departmentRepository->save($departmentAllgemein);
        }

        try {
            $this->generateArtikelForDepartments();
            $this->createMitarbeiter();
        } catch (\Exception $e) {
            throw new \Exception('Es ist ein Fehler aufgetreten: ' . $e->getMessage());
        }

        $io->success('Artikel wurden erfolgreich erstellt.');

        return Command::SUCCESS;
    }

    private function generateArtikelForDepartments()
    {
        $artikelNamen = array(
            array(
                "abteilungname" => "Allgemein",
                "artikelname" => "_Med-Comfort® Style",
                "artikelzusatzinfo" => "PSA; ; Größe: XS - L; div. Farben, Nitrilhandschuhe, unsteril, puderfrei",
                "herstellername" => "AMPri Handelsgesellschaft",
                "refnummer" => "MedComfortStyle",
                "lieferantname" => "TMS Neuhaus",
                "bestellnummer" => "MedComfortStyle",
                "url" => "https://shop.tms-neuhaus.de/med-comfort-style-9560.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Allgemein",
                "artikelname" => "_Schutzscheibe",
                "artikelzusatzinfo" => "PSA; ; Glasscheibe für Absaughalterung an Einzelarbeitsplatz",
                "herstellername" => "",
                "refnummer" => "",
                "lieferantname" => "",
                "bestellnummer" => "",
                "url" => "",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Allgemein",
                "artikelname" => "Sekundenkleber",
                "artikelzusatzinfo" => "Hilfsmittel; ; Cyan-Acrylat-Kleber mit hoher Klebekraft.",
                "herstellername" => "Omnident",
                "refnummer" => "82228",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "82228",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Modellherstellung/Hilfsmittel_Modell/Sekundenkleber/?card=18694",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Allgemein",
                "artikelname" => "Lotio Lind Professional & Care, 500 ml",
                "artikelzusatzinfo" => "PSA; ; Hautschutzcreme / Handcreme",
                "herstellername" => "Dr. Deppe",
                "refnummer" => "700572",
                "lieferantname" => "TMS Neuhaus",
                "bestellnummer" => "A2106186",
                "url" => "https://shop.tms-neuhaus.de/a2106186-lotio-lind-professional-care-500ml.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Allgemein",
                "artikelname" => "Tork F1 extra weiche Kosmetiktücher",
                "artikelzusatzinfo" => "Hilfsmittel; ; weiß, 2-lagig",
                "herstellername" => "Essity Hygiene and Health AB",
                "refnummer" => "140280",
                "lieferantname" => "TMS Neuhaus",
                "bestellnummer" => "TO140280",
                "url" => "https://shop.tms-neuhaus.de/tork-140280.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Allgemein",
                "artikelname" => "Q-Tips",
                "artikelzusatzinfo" => "Hilfsmittel; ; Wattestäbchen, Nachfüllpack 160 Stk",
                "herstellername" => "Q-Tips",
                "refnummer" => "4000576027230",
                "lieferantname" => "Drogeriemarkt (Rossmann, DM)",
                "bestellnummer" => "1570765",
                "url" => "https://www.dm.de/q-tips-wattestaebchen-nachfuellpack-p4000576027230.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Allgemein",
                "artikelname" => "Spülmittel Original, 635 ml",
                "artikelzusatzinfo" => "Hilfsmittel; ; pflanzenbarierte Inhaltsstoffe",
                "herstellername" => "fit",
                "refnummer" => "4013162033294",
                "lieferantname" => "Drogeriemarkt (Rossmann, DM)",
                "bestellnummer" => "1695152",
                "url" => "https://www.dm.de/fit-spuelmittel-original-p4013162033294.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Allgemein",
                "artikelname" => "Watte",
                "artikelzusatzinfo" => "Hilfsmittel; ; chlorfrei gebleicht, frei von Duftstoffen, 100% Viskose",
                "herstellername" => "ebelin",
                "refnummer" => "4066447016765",
                "lieferantname" => "Drogeriemarkt (Rossmann, DM)",
                "bestellnummer" => "1040014",
                "url" => "https://www.dm.de/ebelin-watte-p4066447016765.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Allgemein",
                "artikelname" => "Zahnbürste",
                "artikelzusatzinfo" => "Hilfsmittel; ; abgerundete Borsten, Spezialfederung, rutschfester Griff",
                "herstellername" => "Dr. Best",
                "refnummer" => "5054563925084",
                "lieferantname" => "Drogeriemarkt (Rossmann, DM)",
                "bestellnummer" => "1602173",
                "url" => "https://www.dm.de/dr-best-zahnbuerste-classic-original-mittel-vorteilspack-2-1-gratis-p5054563925084.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "arti-base® 60",
                "artikelzusatzinfo" => "Gipsen; ; weiß",
                "herstellername" => "Dentona",
                "refnummer" => "10505",
                "lieferantname" => "Dentona",
                "bestellnummer" => "10505",
                "url" => "https://dentona.de/dentona/gipse/artikulationsgipse/arti-base-60/10505",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "dento-stone® 220",
                "artikelzusatzinfo" => "Gipsen; ; weiß",
                "herstellername" => "Dentona",
                "refnummer" => "10235",
                "lieferantname" => "Dentona",
                "bestellnummer" => "10235",
                "url" => "https://dentona.de/dentona/dento-stone-220/10235",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "esthetic-base® 300",
                "artikelzusatzinfo" => "Gipsen; ; goldbraun",
                "herstellername" => "Dentona",
                "refnummer" => "10135",
                "lieferantname" => "Dentona",
                "bestellnummer" => "10135",
                "url" => "https://dentona.de/dentona/gipse/stumpfgipse-typ-4/esthetic-base-300/10135",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Gypstray",
                "artikelzusatzinfo" => "Gipsen; ; ph-neutraler Gipslöser",
                "herstellername" => "Zhermack",
                "refnummer" => "C400442",
                "lieferantname" => "Zhermack",
                "bestellnummer" => "C400442",
                "url" => "https://shop.zhermack.de/hygienesysteme/desinfektionsmittel/spezial/1131/gypstray?number=C400442",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Haftplatten für Kunststoffartikulationsplatten",
                "artikelzusatzinfo" => "Gipsen; ; kompatibel zu ADESSOSPLIT, Slitex und Quicksplit",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "10007",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "10007",
                "url" => "https://www.maelzer-dental.de/Haftplatten-fuer-Kunststoffartikulationsplatten/10007",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "DIVARIO® Schleifbandträger, geschlitzt",
                "artikelzusatzinfo" => "Gipsen; ; selbstspreizende Gumiwalze mit Handstück-Schaft",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "15100",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "15100",
                "url" => "https://www.maelzer-dental.de/DIVARIO-Schleifbandtraeger-geschlitzt/15100",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "DIVARIO® Schleifbänder Körnung  80",
                "artikelzusatzinfo" => "Gipsen; ; für Modellherstellung, unterfüttert (50 Stück) …",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "15100-80",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "15100-80",
                "url" => "https://www.maelzer-dental.de/DIVARIO-Schleifbaender-fuer-Modellherstellung-unterfuettert-50-Stueck/15100-80",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "DIVARIO® Schleifbänder Körnung 120",
                "artikelzusatzinfo" => "Gipsen; ; zum Beschleifen von Gipsmodellstümpfen, Trimmen von Modellen, usw.",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "15100-120",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "15100-120",
                "url" => "https://www.maelzer-dental.de/DIVARIO-Schleifbaender-fuer-Modellherstellung-unterfuettert-50-Stueck/15100-120",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "9957R.104.130",
                "artikelzusatzinfo" => "Gipsen; ; Träger zu Aufnahme von Schleifkappen (KST&Gips) [070, 100, 130*]",
                "herstellername" => "Komet Dental",
                "refnummer" => "9957R.104.130",
                "lieferantname" => "Komet Dental",
                "bestellnummer" => "9957R.104.130",
                "url" => "https://www.kometstore.de/de-de/products/products-kometdental/9957r.aspx",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "9958RA.000.130",
                "artikelzusatzinfo" => "Gipsen; ; Schleifkappe (KST&Gips) [070, 100 130*]",
                "herstellername" => "Komet Dental",
                "refnummer" => "9958RA.000.130",
                "lieferantname" => "Komet Dental",
                "bestellnummer" => "9958RA.000.130",
                "url" => "https://www.kometstore.de/de-de/products/products-kometdental/9958ra.aspx",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "_Temdent classic hell",
                "artikelzusatzinfo" => "Gipsen; ; 100 g Pulver",
                "herstellername" => "Schütz Dental",
                "refnummer" => "220021",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "11424",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Praxis/Abformung-ProthetikP/KB-Materialien_prov/Temdent-Set+Classic/?card=171641",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "_Temdent classic liquid",
                "artikelzusatzinfo" => "Gipsen; ; 100ml Flüssigkeit",
                "herstellername" => "Schütz Dental",
                "refnummer" => "220020(?)",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "11424",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Praxis/Abformung-ProthetikP/KB-Materialien_prov/Temdent-Set+Classic/?card=171641",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "_Temdent-Set Classic",
                "artikelzusatzinfo" => "Gipsen; ; jeweils 100g Pulver & 100ml Flüssigkeit",
                "herstellername" => "Schütz Dental",
                "refnummer" => "220010",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "11424",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Praxis/Abformung-ProthetikP/KB-Materialien_prov/Temdent-Set+Classic/?card=171641",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "PENTRON Alginat IMAGE",
                "artikelzusatzinfo" => "Abformung; ; normalhärtend grün 500g Dose",
                "herstellername" => "Kerr",
                "refnummer" => "27426A",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "35932",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Praxis/Abformung-ProthetikP/Alginate/PENTRON+Alginat+IMAGE/?card=224",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Algitray",
                "artikelzusatzinfo" => "Abformung; ; ph-neutraler Alginatlöser",
                "herstellername" => "Zhermack",
                "refnummer" => "C400432",
                "lieferantname" => "Zhermack",
                "bestellnummer" => "C400432",
                "url" => "https://shop.zhermack.de/hygienesysteme/desinfektionsmittel/spezial/4191/algitray",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "coppie-flux",
                "artikelzusatzinfo" => "Abformung; ; Entspanner für Wachs und Silikon - Nachfüllflasche 1000ml",
                "herstellername" => "Dentona",
                "refnummer" => "61001",
                "lieferantname" => "Dentona",
                "bestellnummer" => "61001",
                "url" => "https://dentona.de/dentona/zubehoer/coppie-flux/61001",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Magnettopf gedreht",
                "artikelzusatzinfo" => "Artikulation; ; vernickelter Stahl",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "10061",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "10061",
                "url" => "https://www.maelzer-dental.de/Magnettopf-gedreht/10061",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Magnet für Splitcast 20 x 6 mm",
                "artikelzusatzinfo" => "Artikulation; ; ",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "10055",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "10055",
                "url" => "https://www.maelzer-dental.de/Magnet-fuer-Splitcast-20-x-6-mm/10055",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Retentionsscheiben gestanzt",
                "artikelzusatzinfo" => "Artikulation; ; vernickelt, für Splitccast-Technik, 25x1,5mm",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "10150",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "10150",
                "url" => "https://www.maelzer-dental.de/search?search=10150",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "ARTIDISC®-S ",
                "artikelzusatzinfo" => "Artikulation; ; Kunststoffartikulationsplatten verw. für Splitex® (100 Stück) weiß",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "10267",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "10267",
                "url" => "https://www.maelzer-dental.de/ARTIDISC-S-Kunststoffartikulationsplatten-verw.-fuer-Splitex-100-Stueck/10267",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Artigator Modellplatte",
                "artikelzusatzinfo" => "Artikulation; ; blau 50er Pack",
                "herstellername" => "Amann Girrbach",
                "refnummer" => "218941",
                "lieferantname" => "Amann Girrbach",
                "bestellnummer" => "218941",
                "url" => "https://ag.store/de/artigator-basisplatte-blau-428",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "dento-sol",
                "artikelzusatzinfo" => "Artikulation; ; Isoliermittel Nachfüllflasche 1000ml",
                "herstellername" => "Dentona",
                "refnummer" => "60001",
                "lieferantname" => "Dentona",
                "bestellnummer" => "60001",
                "url" => "https://dentona.de/dentona/zubehoer/dento-sol/60001",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "DIVARIO® Sockelplatten Premium klein grau",
                "artikelzusatzinfo" => "Artikulation; ; ",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "14030GR",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "14030GR",
                "url" => "https://www.maelzer-dental.de/DIVARIO-Sockelplatten-Premium-100-Stueck/14030GR",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "DIVARIO® Sockelplatten Premium groß grau",
                "artikelzusatzinfo" => "Artikulation; ; ",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "14040GR",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "14040GR",
                "url" => "https://www.maelzer-dental.de/DIVARIO-Sockelplatten-Premium-100-Stueck/14040GR",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Splitcastplatten für DIVARIO® Sockelplatte (50 Stück) klein",
                "artikelzusatzinfo" => "Artikulation; ; ",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "14050",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "14050",
                "url" => "https://www.maelzer-dental.de/Splitcastplatten-fuer-DIVARIO-Sockelplatte-50-Stueck/14050",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Splitcastplatten für DIVARIO® Sockelplatte (50 Stück) groß",
                "artikelzusatzinfo" => "Artikulation; ; ",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "14060",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "14060",
                "url" => "https://www.maelzer-dental.de/Splitcastplatten-fuer-DIVARIO-Sockelplatte-50-Stueck/14060",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "DIVARIO® Bohrer HM",
                "artikelzusatzinfo" => "Zeiser; ; Hartmetallfräser für das DIVARIO-Pinbohrgerät",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "10171",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "10171",
                "url" => "https://www.maelzer-dental.de/DIVARIO-Bohrer-HM/10171",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "DIVARIO® Pins 14mm",
                "artikelzusatzinfo" => "Zeiser; ; längere und stärkere Pins für Sägemodelle",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "10172",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "10172",
                "url" => "https://www.maelzer-dental.de/DIVARIO-Pins/10172",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "DIVARIO® Pins 12,5mm",
                "artikelzusatzinfo" => "Zeiser; ; kürzere und grazilere Pins für Sägemodelle",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "10172-K",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "10172-K",
                "url" => "https://www.maelzer-dental.de/DIVARIO-Pins/10172-K",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "DIVARIO® Putty für die Modellherstellung",
                "artikelzusatzinfo" => "Zeiser; ; Dauerweiche, erdfarbene Silikon-Knetmasse",
                "herstellername" => "Mälzer Dental",
                "refnummer" => "14070",
                "lieferantname" => "Mälzer Dental",
                "bestellnummer" => "14070",
                "url" => "https://www.maelzer-dental.de/DIVARIO-Putty-fuer-die-Modellherstellung/14070",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Mischkanülen transparent 1:1",
                "artikelzusatzinfo" => "Abformung; ; 100 Stück",
                "herstellername" => "Dreve Dentamid",
                "refnummer" => "D49601",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "47524",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Praxis/Einwegartikel/Mischkanuelen/Mischkanuelen/?card=188362",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "_Filterbeutel für Trimmer",
                "artikelzusatzinfo" => "Filter; ; Papierfilterbeutel für den Trockentrimmer",
                "herstellername" => "",
                "refnummer" => "",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "",
                "url" => "",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "_Filtermatte",
                "artikelzusatzinfo" => "Filter; ; Vliesfilter für Ausbrüher",
                "herstellername" => "",
                "refnummer" => "",
                "lieferantname" => "Baumarkt (Hornbach, Bauhaus, Toom) ",
                "bestellnummer" => "",
                "url" => "https://www.hornbach.de/p/fettfilter-zuschneidbar/5850511/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "_Flüssigseife Nachfüllpack/Spenderflasche klein",
                "artikelzusatzinfo" => "Reinigung; ; ",
                "herstellername" => "",
                "refnummer" => "",
                "lieferantname" => "Drogeriemarkt (Rossmann, DM)",
                "bestellnummer" => "",
                "url" => "",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "TMS Quick Flüssigseife, 5 Liter",
                "artikelzusatzinfo" => "Reinigung; ; Hand- und Körperwaschmittel, PH-Wert: 6, Farbe: rosa",
                "herstellername" => "Polymer-Chemie Klaus Frerick e.K.",
                "refnummer" => "3530010",
                "lieferantname" => "TMS Neuhaus",
                "bestellnummer" => "A2112937",
                "url" => "https://shop.tms-neuhaus.de/a2112808-tms1quick1fluessigseife-5l.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "OP Sept Classic, 500 ml",
                "artikelzusatzinfo" => "Reinigung; ; Händedesinfektion, alkoholisch",
                "herstellername" => "Dr. Deppe",
                "refnummer" => "601121",
                "lieferantname" => "TMS Neuhaus",
                "bestellnummer" => "M2114389",
                "url" => "https://shop.tms-neuhaus.de/m2114389-op-sept-classic-500-ml.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "_ORBI-Sept Abformdesinfektion",
                "artikelzusatzinfo" => "Reinigung; ; Abformdesinfektion, geeignet für Ultraschallbad",
                "herstellername" => "ORBIS Dental",
                "refnummer" => "262558",
                "lieferantname" => "ORBIS Dental",
                "bestellnummer" => "262558",
                "url" => "https://shop.orbis-dental.de/de/artikel/orbi-sept-abformdesinfektion-262558.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Siiladent Ultraschall-Polierpastenreiniger 1,0 kg",
                "artikelzusatzinfo" => "Reinigung; ; Hochkonzentrat 1:40, wasserlöslicher Reiniger",
                "herstellername" => "Siladent",
                "refnummer" => "251021",
                "lieferantname" => "Siladent",
                "bestellnummer" => "251021",
                "url" => "https://www.siladent-shop.de/ultraschall-polierpastenreiniger-p-1001.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Arbeitsvorbereitung",
                "artikelname" => "Papernet Küchenrollen",
                "artikelzusatzinfo" => "Reinigung; ; weiß, 3-lagig, 51-blatt, 26x22x11, Romb-Circle-Prägung",
                "herstellername" => "Sofidel Germany",
                "refnummer" => "416596",
                "lieferantname" => "TMS Neuhaus",
                "bestellnummer" => "A2108655",
                "url" => "https://shop.tms-neuhaus.de/a2108655-papernet-kuechenrollen.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "GC G-CEM ONE Twin A2 Refill",
                "artikelzusatzinfo" => "Verbindung; ; selbstadhäsives Befestigungs-Composite",
                "herstellername" => "GC Germany",
                "refnummer" => "013664",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "210665",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Praxis/Fuellungen/Befestigungsmaterial/GC+G-CEM+ONE/?card=160433",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Contex®",
                "artikelzusatzinfo" => "Verbindung; ; Anti-Flussmittel für Edelmetall-Lötungen",
                "herstellername" => "Degudent",
                "refnummer" => "5325240004",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "61391",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Einbetten/Loetutensilien/Contex%C2%AE/?card=103291",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Aceton (NEUTRAL Aceton)",
                "artikelzusatzinfo" => "Reinigung; ; Verdünner 1l, EAN 4003498365794",
                "herstellername" => "Meffert AG",
                "refnummer" => "78307576700000",
                "lieferantname" => "Hornbach",
                "bestellnummer" => "10219174",
                "url" => "https://www.hornbach.de/p/aceton-1l/10219174/?searchTerm=aceton",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Destiliertes Wasser",
                "artikelzusatzinfo" => "Reinigung; ; Kanister 5l,  EAN 4027217005075",
                "herstellername" => "C+V Pharma-Depot",
                "refnummer" => "4 027217000056",
                "lieferantname" => "Hornbach",
                "bestellnummer" => "8848107",
                "url" => "https://www.hornbach.de/p/destilliertes-wasser-5-l-volldemineralisiert/8848107/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "brasil 22",
                "artikelzusatzinfo" => "Dublieren; ; Additionsvernetztes Dubliersilikon 1:1 blau+orange->samtbraun",
                "herstellername" => "Dentona",
                "refnummer" => "16005",
                "lieferantname" => "Dentona",
                "bestellnummer" => "16005",
                "url" => "https://dentona.de/dentona/silikone/brasil-22/16005.M",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "DIAgel LC Modellierkunststoff lichthärtend",
                "artikelzusatzinfo" => "Modellieren; ; rottransparenter, lichthärtender Kunststoff",
                "herstellername" => "Dental Kontor",
                "refnummer" => "26-0550",
                "lieferantname" => "Dental Kontor",
                "bestellnummer" => "26-0550",
                "url" => "https://www.dentalkontor.de/diagel-lc.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Silikon- &, Wachsentspanner",
                "artikelzusatzinfo" => "Modellieren; ; Flasche 500ml",
                "herstellername" => "DFS-Diamon",
                "refnummer" => "25030",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "86358",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Modellieren/Netzmittel_Isolierun/Silikon-+&amp;amp+Wachsentspanner/?card=129717",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Fegupol 8059",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; 40g Dose",
                "herstellername" => "feguramed",
                "refnummer" => "8059",
                "lieferantname" => "feguramed",
                "bestellnummer" => "8059",
                "url" => "https://www.feguramed.de/de/prod/polierpasten/31829-fegupol-8059.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Dental Vlies",
                "artikelzusatzinfo" => "Einbetten; ; Muffelring-Einlagen, asbestfrei, aus keramischen Werkstoffen",
                "herstellername" => "Omnident",
                "refnummer" => "87907",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "87907",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Einbetten/Muffelsysteme/Dental+Vlies/?card=20107",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Metall-Muffelringe Gr. 6",
                "artikelzusatzinfo" => "Einbetten; ; für Einbettmassen der Fa. BEGO u.a.",
                "herstellername" => "BEGO",
                "refnummer" => "52423 (4)",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "44909",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Einbetten/Muffelsysteme/Metall-Muffelringe/?card=98203",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Arti-Fol® Plastik 8µ 1-seitig",
                "artikelzusatzinfo" => "Hilfsmittel; Okklusion; Spender 20m x 75mm x 8µm, rot",
                "herstellername" => "Bausch",
                "refnummer" => "BK 71",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "70268",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Praxis/Hilfsmittel_Fuellung/OcclufolieP/Arti-Fol%C2%AE+Plastik+8%C2%B5+1-seitig",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Oscar Diamant Polierpaste",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; AP Dental",
                "herstellername" => "Aesthetic Press",
                "refnummer" => "9100",
                "lieferantname" => "AP Dental",
                "bestellnummer" => "9100",
                "url" => "https://www.apdental.shop/product-page/oscar-diamant-polierpaste",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Tork H2 Xpress® Papierhandtücher",
                "artikelzusatzinfo" => "Reinigen; ; extra weiche Multifold Handtücher, weiß, 2-lagig, 2100 Tücher",
                "herstellername" => "Essity Hygiene and Health AB",
                "refnummer" => "100297",
                "lieferantname" => "TMS Neuhaus",
                "bestellnummer" => "TO100297",
                "url" => "https://shop.tms-neuhaus.de/tork-100297.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Tork H3 Zickzack Papierhandtücher",
                "artikelzusatzinfo" => "Reinigen; ; weiß, 2-lagig, 3750 Tücher",
                "herstellername" => "Essity Hygiene and Health AB",
                "refnummer" => "290163",
                "lieferantname" => "TMS Neuhaus",
                "bestellnummer" => "TO290163",
                "url" => "https://shop.tms-neuhaus.de/tork-290163.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Pattern Resin LS Komplettset",
                "artikelzusatzinfo" => "Modellieren; ; 100g Pulver, 105ml Flüssigkeit, 2 Anmischbecher, 1 Pinsel No.4, 1 Pipette",
                "herstellername" => "GC Germany",
                "refnummer" => "335201",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "21796",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Modellieren/Modellierkunststoffe/PATTERN+RESIN+LS/?shop_category=&card=5216&var=true",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Pattern Resin LS Flüssigkeit",
                "artikelzusatzinfo" => "Modellieren; ; große Nachfüllflasche 262ml",
                "herstellername" => "GC Germany",
                "refnummer" => "335205",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "21797",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Modellieren/Modellierkunststoffe/PATTERN+RESIN+LS/?shop_category=&card=5217&var=true",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Pattern Resin LS Pulver",
                "artikelzusatzinfo" => "Modellieren; ; große Nachfüllpackung 1kg",
                "herstellername" => "GC Germany",
                "refnummer" => "335204",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "67090",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Modellieren/Modellierkunststoffe/PATTERN+RESIN+LS/?shop_category=&card=15108&var=true",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "rema® Flux 1",
                "artikelzusatzinfo" => "Verbindung; ; Flussmittel für CoCrMo-Sold 1 und NiCr-Sold 1",
                "herstellername" => "Dentaurum",
                "refnummer" => "102-304-00",
                "lieferantname" => "Henry Schein",
                "bestellnummer" => "3163800",
                "url" => "https://www.henryschein-dental.de/global/Shopping/ProductDetailsFullPage.aspx?productid=3163800&CatalogName=WEBDENT&name=rema&",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Diasol Okklusionsspray mit Metalldüse",
                "artikelzusatzinfo" => "Hilfsmittel; ; NUR  IN HELLGRÜN",
                "herstellername" => "Dental Kontor",
                "refnummer" => "26-0032",
                "lieferantname" => "Dental Kontor",
                "bestellnummer" => "26-0032",
                "url" => "https://www.dentalkontor.de/diasol-okklusionsspray-mit-metallduese.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Diasol Okklusionsspray mit Kunststoffdüse",
                "artikelzusatzinfo" => "Hilfsmittel; ; weiß",
                "herstellername" => "Dental Kontor",
                "refnummer" => "26-1001",
                "lieferantname" => "Dental Kontor",
                "bestellnummer" => "26-1001",
                "url" => "https://www.dentalkontor.de/diasol-okklusionsspray-mit-kunststoffduese.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Oxynon®",
                "artikelzusatzinfo" => "Verbindung; Löten; Spezial-Flussmittel für NEM in Pastenform, braun 50ml",
                "herstellername" => "Degudent",
                "refnummer" => "5325310004",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "69779",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Einbetten/Loetutensilien/Oxynon%C2%AE/?card=103292",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Cera Blast, 25kg 50µm",
                "artikelzusatzinfo" => "Einbetten; ; Feinstrahlperlen zum Glanzstrahlen",
                "herstellername" => "Omnident",
                "refnummer" => "88172",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "88172",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Ausarbeiten_Polieren/Strahlmittel_Duesen/Cera+Blast/?card=20173",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Alustral, 25kg 110µm",
                "artikelzusatzinfo" => "Einbetten; ; hochwert. synth. Strahlmittel aus Aluminiumoxyd. Keine Silikosegefahr.",
                "herstellername" => "Omnident",
                "refnummer" => "78326",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "78326",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Ausarbeiten_Polieren/Strahlmittel_Duesen/Alustral/?card=17816",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "Alustral, 25kg 250µm",
                "artikelzusatzinfo" => "Einbetten; ; hochwert. synth. Strahlmittel aus Aluminiumoxyd. Keine Silikosegefahr.",
                "herstellername" => "Omnident",
                "refnummer" => "78328",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "78328",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Ausarbeiten_Polieren/Strahlmittel_Duesen/Alustral/?shop_category=&card=17818",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "_Stahlgoldlot 935",
                "artikelzusatzinfo" => "Verbindung; Löten; nickelhaltiges Lot zum Verbinden von zahntechnischen Arbeiten",
                "herstellername" => "C.Hafner",
                "refnummer" => "",
                "lieferantname" => "C.Hafner",
                "bestellnummer" => "",
                "url" => "https://www.c-hafner.de/technologien-werkstoffe/werkstoffe/dental-lote.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "_CH-Universal-Lot 770",
                "artikelzusatzinfo" => "Verbindung; Löten; Arbeitstemperatur 770°C",
                "herstellername" => "C.Hafner",
                "refnummer" => "",
                "lieferantname" => "C.Hafner",
                "bestellnummer" => "",
                "url" => "https://www.c-hafner.de/technologien-werkstoffe/werkstoffe/dental-lote.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "DD LSTG 930",
                "artikelzusatzinfo" => "Verbindung; Löten; Dentaldirekt Lot Arbeitstemperatur 930°C",
                "herstellername" => "C.Hafner",
                "refnummer" => "",
                "lieferantname" => "Bauer-Walser AG",
                "bestellnummer" => "LSTG930",
                "url" => "https://www.c-hafner.de/fileadmin/user_upload/pdf/dental-legierungen/Gebrauchsanweisung_DD_Lote.pdf",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "_Mig-O-Mat microflame Brennerdüsen, ø 1,0 x 10 mm (G19) - 5 Stück",
                "artikelzusatzinfo" => "Verbindung; Löten; Aufsätze für microbrenner-Handstücke",
                "herstellername" => "Mig-O-Mat",
                "refnummer" => "",
                "lieferantname" => "DT&shop",
                "bestellnummer" => "42488",
                "url" => "https://www.dt-shop.com/index.php?id=22&L=0&artnr=42488&aw=197&pg=5&geoipredirect=1",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Edelmetall",
                "artikelname" => "_Mig-O-Mat microflame Brennerdüsen, ø 1,8 x 10 mm (G15) - 5 Stück",
                "artikelzusatzinfo" => "Verbindung; Löten; Aufsätze für microbrenner-Handstücke",
                "herstellername" => "Mig-O-Mat",
                "refnummer" => "",
                "lieferantname" => "DT&shop",
                "bestellnummer" => "42494",
                "url" => "https://www.dt-shop.com/index.php?id=22&L=0&artnr=42488&aw=197&pg=5&geoipredirect=1",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Isopropanol 99,9%",
                "artikelzusatzinfo" => "Reinigen; ; Reinigungsalkohol",
                "herstellername" => "Höfer Chemie",
                "refnummer" => " 10020006",
                "lieferantname" => "Amazon",
                "bestellnummer" => "",
                "url" => "https://amzn.eu/d/06Dsefi0",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Isopropylalkohol 70% Hetterich",
                "artikelzusatzinfo" => "Reinigen; ; Desinfektionsmittel zum Auftragen auf die Haut",
                "herstellername" => "Teofarma srl",
                "refnummer" => "PZN-4769708",
                "lieferantname" => "Amazon / Apotheke",
                "bestellnummer" => "",
                "url" => "https://amzn.eu/d/0bBfYTwH",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "_Acryline X-Ray DVT Powder 100g",
                "artikelzusatzinfo" => "Kunststoff; ; für digitale Volumentomographie röntgengeeignetes Kunststoffmaterial",
                "herstellername" => "anax dent",
                "refnummer" => "17090100",
                "lieferantname" => "",
                "bestellnummer" => "",
                "url" => "https://www.anaxdent.com/Produkte/Kunststoffe/Radioopake-Kunststoffe/Acryline-X-Ray-DVT-Powder-100g",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "_Acryline Powder clear 100g",
                "artikelzusatzinfo" => "Kunststoff; ; glasklares Acryline als Basismaterial für Schienen und Bohrschablonen",
                "herstellername" => "anax dent",
                "refnummer" => "17010100",
                "lieferantname" => "",
                "bestellnummer" => "",
                "url" => "https://www.anaxdent.com/Produkte/Kunststoffe/Prothesenkunststoffe/Acryline-Powder-clear-100g",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "_Acryline Liquid 100ml",
                "artikelzusatzinfo" => "Kunststoff; ; Monomerflasche",
                "herstellername" => "anax dent",
                "refnummer" => "17100100",
                "lieferantname" => "",
                "bestellnummer" => "",
                "url" => "https://www.anaxdent.com/Produkte/Kunststoffe/Prothesenkunststoffe/Acryline-Liquid-100ml",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "_New Outline Dentin [Farbe] A3",
                "artikelzusatzinfo" => "Kunststoff; ; Zahnfarbener Polymermethylmethacrylat (Pulver)",
                "herstellername" => "anax dent",
                "refnummer" => "16013100",
                "lieferantname" => "",
                "bestellnummer" => "",
                "url" => "https://www.anaxdent.com/Produkte/Kunststoffe/Provisorienkunststoffe/New-Outline-Dentin-A3-100g",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "_New Outline Liquid 100ml",
                "artikelzusatzinfo" => "Kunststoff; ; Monomerflüssigkeit",
                "herstellername" => "anax dent",
                "refnummer" => "16100100",
                "lieferantname" => "",
                "bestellnummer" => "",
                "url" => "https://www.anaxdent.com/Produkte/Kunststoffe/Provisorienkunststoffe/New-Outline-Liquid-100ml",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Aislar® 500ml",
                "artikelzusatzinfo" => "Hilfsmittel; Isolierung; formaldehydfreie Gipsisolierung auf Alginatbasis gegen KST und Komposite",
                "herstellername" => "Kulzer",
                "refnummer" => "64708057",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "74196",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Modellieren/Netzmittel_Isolierun/Aislar/?card=17064",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "_anaxblend Dentin A3 Flow 3g",
                "artikelzusatzinfo" => "Kunststoff; Composite; lichthärtendes Dentinmaterial in Spritzenform",
                "herstellername" => "anax dent",
                "refnummer" => "20213003",
                "lieferantname" => "",
                "bestellnummer" => "",
                "url" => "https://www.anaxdent.com/Produkte/Komposite/Verblendmaterialien/anaxblend-Dentin-A3-Flow-3g",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Bimssteinpulver grob 25",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; quarzfreies (keine Silikosegefahr) Pulver mit hohem Schleifwirkungsgrad",
                "herstellername" => "Ernst Hinrichs",
                "refnummer" => "100374",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "63759",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Ausarbeiten_Polieren/Polierpasten/Bimssteinpulver/?shop_category=&card=14196",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Bimsdesinfektion 5 Liter",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; formaldehydfreies Desinfektionsmittel, fungizid, bakterizid, tuberkulozid",
                "herstellername" => "Ernst Hinrichs",
                "refnummer" => "103802",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "24102",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Ausarbeiten_Polieren/Polierpasten/Bimsdesinfektion/?card=5831",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "CERAMAGE UP Body A3B",
                "artikelzusatzinfo" => "Kunststoff; Composite; fließfähiges Komposit-Verblendmaterial - Dentinmasse",
                "herstellername" => "Shofu Dental",
                "refnummer" => "2337",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "120185",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Verblendtechnik/Verblendkunststoffe/CERAMAGE+UP+Body/?card=92198",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "CERAMAGE UP Incisal 59 ",
                "artikelzusatzinfo" => "Kunststoff; Composite; fließfähiges Komposit-Verblendmaterial - Schneidemasse",
                "herstellername" => "Shofu Dental",
                "refnummer" => "2358",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "119653",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Verblendtechnik/Verblendkunststoffe/CERAMAGE+UP+Incisal/?card=91996",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Ceramage Modellierflüssigkeit",
                "artikelzusatzinfo" => "Kunststoff; Composite; 6ml Modellierflüssigkeit zur Schichtung von Komposit",
                "herstellername" => "Shofu Dental",
                "refnummer" => "1991",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "64850",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Verblendtechnik/Kunststofffluessig/Ceramage+Modellierfluessigkeit/?card=14480",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Ceramage Dentin A3B",
                "artikelzusatzinfo" => "Kunststoff; Composite; 4,6g Lichthärtendes Mikro-Hybrid-Komposit - Dentinmasse",
                "herstellername" => "Shofu Dental",
                "refnummer" => "1903",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "63813",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Verblendtechnik/Verblendkunststoffe/Ceramage+Dentin/?card=14207",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Ceramage Schneide 59 ",
                "artikelzusatzinfo" => "Kunststoff; Composite; 4,6g Lichthärtendes Mikro-Hybrid-Komposit - Schneidemasse",
                "herstellername" => "Shofu Dental",
                "refnummer" => "1894",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "63560",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Verblendtechnik/Verblendkunststoffe/Ceramage+Schneide/?card=14151",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Universal Opaque PRE-OPAQUE",
                "artikelzusatzinfo" => "Kunststoff; Composite; 2ml Spritze mit lichthärtendem Pastenopaker zur Grundierung von Gerüsten",
                "herstellername" => "Shofu Dental",
                "refnummer" => "2111",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "108407",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Verblendtechnik/Verblendkunststoffe/Universal+Opaque/?shop_category=&card=111743&var=true",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Fegupol Compo+",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; Polierpaste für Komposit-Werkstoffe",
                "herstellername" => "feguramed",
                "refnummer" => "8070",
                "lieferantname" => "",
                "bestellnummer" => "",
                "url" => "https://www.feguramed.de/de/prod/polierpasten/31834-fegupol-compo.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "N-Klammer ∅0,9mm",
                "artikelzusatzinfo" => "Klammerdraht; Klammern; Doppelarmklammer mit Auflagenansatz aus Edelstahl",
                "herstellername" => "Scheu-Dental",
                "refnummer" => "1009.1",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "75850",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/KFO/Draehte_Buegel/N-Klammer/?card=114675",
                "val8" => "https://scheu-dental.com/produkte/klammer-und-buegeltechnik/n-klammer/1009"
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "_Verstärkungsnetz, fein, vergoldet",
                "artikelzusatzinfo" => "Hilfsteil; Verstärkung; feinmaschiges Netz für Kunststoffprothesen, 500x100x0,4mm, Rolle",
                "herstellername" => "Renfert",
                "refnummer" => "2222100",
                "lieferantname" => "",
                "bestellnummer" => "",
                "url" => "https://www.renfert.com/de/produkte/materialien/verstaerkungseinlagen/verstaerkungsnetze-gitter",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Dura-Polish DIA",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; Hochglanzpolitur von Komposit und Keramik, Zirkoniumdioxid und Metallen",
                "herstellername" => "Shofu Dental",
                "refnummer" => "0554",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "64857",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Ausarbeiten_Polieren/Polierpasten/DURA-POLISH+DIA/?card=14486",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "ECO-JECT Einmalspritzen 20ml",
                "artikelzusatzinfo" => "Hilfsmittel; Einlaufen; 2-teilige Spritzen der Fa. Dispomed/Unigloves",
                "herstellername" => "Dispomed",
                "refnummer" => "20020",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "123873",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Praxis/Einwegartikel/Einmalspritzen/ECO-JECT+Einmalspritzen/?card=196543",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Ersatzfilter f. Einzelplatzabsaugungen",
                "artikelzusatzinfo" => "Hilfsmittel; Absaugen; Zubehör für SMARTair™ Evo",
                "herstellername" => "KaVo Dental",
                "refnummer" => "0.658.2160",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "74206",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/GeraetezubehoerL/Siebe_FilterL/Ersatzfilter+f+Einzelplatzabsaugungen/?card=17068",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Filzkegel, unmontiert, Ø20mm, Höhe 40mm ",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; Hochglanz-Poliermittel für alle Materialien",
                "herstellername" => "Polirapid",
                "refnummer" => "FKU 20 200",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "53348",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Ausarbeiten_Polieren/Polierbuersten_unmon/Filzkegel+unmontiert/?card=187817",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "OPTIGLAZE color clear",
                "artikelzusatzinfo" => "Kunststoff; Composite; 5ml Flasche mit lichthärtender Oberflächenversiegelung",
                "herstellername" => "GC Germany",
                "refnummer" => "008424",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "115632",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Praxis/CAD-CAMP/Cerec-ZubehoerP/OPTIGLAZE+color/?card=61682",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Pattex Hotmelt Heißklebesticks 50 Stück",
                "artikelzusatzinfo" => "Hilfsmittel; Kleben; ",
                "herstellername" => "Henkel",
                "refnummer" => "2004002984007",
                "lieferantname" => "Hornbach",
                "bestellnummer" => "4002984",
                "url" => "https://www.hornbach.de/p/pattex-hotmelt-heissklebesticks-50-stueck/4002984/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "ISO-C, Flasche 500ml ",
                "artikelzusatzinfo" => "Hilfsmittel; Isolierung; hauchdünner Isolierfilm für Wachs auf Gips, Metall, Kunststoff und Epoxy",
                "herstellername" => "Spiess",
                "refnummer" => "0500",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "74751",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Modellieren/Netzmittel_Isolierun/ISO-C/?card=17166",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "J-Klammer Ø 0,9mm - 10 Stück",
                "artikelzusatzinfo" => "Klammerdraht; Klammern; elastische und stabile Klammerkreuze aus hochwertigem Edelstahl",
                "herstellername" => "Scheu-Dental",
                "refnummer" => "1018.1",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "75856",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/KFO/Draehte_Buegel/J-Klammer/?card=114832",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "SCHEU-Anker Ø 0,9mm - 10 Stück",
                "artikelzusatzinfo" => "Klammerdraht; Klammern; Kugelknopfanker",
                "herstellername" => "Scheu-Dental",
                "refnummer" => "2051.1",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "75870",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/KFO/Draehte_Buegel/SCHEU-Anker/?card=114915",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Menzanium® Spulendraht, federhart - 0,9mm, 10m",
                "artikelzusatzinfo" => "Klammerdraht; Spulen; Spulendrähte aus einer nickelfreien Edelstahllegierung für die Prothetik ",
                "herstellername" => "Scheu-Dental",
                "refnummer" => "8463.1",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "45471",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/KFO/Draehte_Buegel/MENZANIUM%C2%AE+Spulendraht/?shop_category=&card=88699",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "remanium® Draht, halbrund, 1,75 x 0,9mm, 10m",
                "artikelzusatzinfo" => "Klammerdraht; Spulen; aus Edelstahl ",
                "herstellername" => "Dentaurum",
                "refnummer" => "308-518-00",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "70922",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/KFO/Draehte_Buegel/remanium%C2%AE+Draht+halbrund/?card=193000",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "SUPERSIL Knetsilikon",
                "artikelzusatzinfo" => "Hilfsmittel; Dublieren; Präzisionssilikon, additionsvernetztes Polyvinylsiloxan, klebefrei, kochfest",
                "herstellername" => "Dental Kontor",
                "refnummer" => "41-0011",
                "lieferantname" => "Dental Kontor",
                "bestellnummer" => "41-0011",
                "url" => "https://www.dentalkontor.de/supersil.html",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "KMG Liquid",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; Universal-Hochglanzpoliermittel für Prthesenkunststoffe und Metalle",
                "herstellername" => "Candulor",
                "refnummer" => "693113",
                "lieferantname" => "Candulor",
                "bestellnummer" => "693113",
                "url" => "https://de.shop.candulor.com/de-de/kmg-liquid",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Wildlederschwabbel, Kunststoffkern Ø90mm ",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; aus Chamoisleder und konischem KST-Kern",
                "herstellername" => "Polirapid",
                "refnummer" => "SWK 00 100",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "60105",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Ausarbeiten_Polieren/Polierbuersten_unmon/Wildlederschwabbel/?card=13388",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "_LITE ART Color Paste ",
                "artikelzusatzinfo" => "Kunststoff; Composite; ",
                "herstellername" => "Shofu Dental",
                "refnummer" => "PN1974",
                "lieferantname" => "",
                "bestellnummer" => "",
                "url" => "https://www.shofu.de/produkt/lite-art/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Xthetic® prime Pulver 1000g",
                "artikelzusatzinfo" => "Kunststoff; PMMA; hochwertige Prothesenkunststoff für die Total- und Teilprothetik - Polymer",
                "herstellername" => "AcrylX",
                "refnummer" => "1-341-100-34",
                "lieferantname" => "Briegel Dental",
                "bestellnummer" => "1-341-100-34",
                "url" => "https://briegeldental.de/shop/acrylx-xthetic-prime-premiumprothesenkunststoff/",
                "val8" => "https://www.acrylx.com/de/Produkte/Classic/Prothesenkunststoffe/Xthetic_prime"
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Xthetic® prime Flüssigkeit 500ml",
                "artikelzusatzinfo" => "Kunststoff; PMMA; hochwertige Prothesenkunststoff für die Total- und Teilprothetik - Monomer",
                "herstellername" => "AcrylX",
                "refnummer" => "1-342-050",
                "lieferantname" => "Briegel Dental",
                "bestellnummer" => "1-342-050",
                "url" => "https://briegeldental.de/shop/acrylx-xthetic-prime-premiumprothesenkunststoff/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Universal Primer",
                "artikelzusatzinfo" => "Kunststoff; Verbinden; Haftvermittler zw. Kunststoff,  Legierungen, Zirkonoxid u. Aluminiumoxid",
                "herstellername" => "Shofu Dental",
                "refnummer" => "Y0060",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "206785",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Praxis/Fuellungen/Bondings_Aetzmittel/Universal+Primer/?card=149758",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Premium Löffelplatten",
                "artikelzusatzinfo" => "Kunststoff; ; Form: Oberkiefer, Farbe; rosa, geringe Dispersionsschicht",
                "herstellername" => "Briegel Dental",
                "refnummer" => "100121",
                "lieferantname" => "Briegel Dental",
                "bestellnummer" => "100121",
                "url" => "https://briegeldental.de/shop/premiumloeffelplatten/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "OVS II Intensiv-Opaker, 10ml, O4 = rosa",
                "artikelzusatzinfo" => "Kunststoff; Composite; Gebrauchsfertiger, selbsthärtender Opaker für Biodent® K+B Plus.",
                "herstellername" => "Dentsply Sirona",
                "refnummer" => "D08235O4",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "37328",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Verblendtechnik/Verblendkunststoffe/OVS+II+Intensiv-Opaker/?card=8753",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "OVS II Opaker-Verdünner, 10ml ",
                "artikelzusatzinfo" => "Kunststoff; Composite; Verdünner für OVS II Opaker",
                "herstellername" => "Dentsply Sirona",
                "refnummer" => "D08235N",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "37329",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Verblendtechnik/Kunststofffluessig/OVS+II+Opaker-Verduenner/?card=8754",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Calibris",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; Polierbürste, 3-reihig mit Leineneinlagen,  KSTkern, Chunkingborsten-Besatz",
                "herstellername" => "Attenborough Dental",
                "refnummer" => "BR50",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "85121",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Ausarbeiten_Polieren/Polierbuersten_unmon/Calibris/?card=19343",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Slim Polierbürsten, 12er Pack ",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; ∅ 45 mm",
                "herstellername" => "Renfert",
                "refnummer" => "7881000",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "61080",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Ausarbeiten_Polieren/Polierbuersten_unmon/Slim+Polierbuersten/?card=13603",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Schmalbürsten Ziegenhaar",
                "artikelzusatzinfo" => "Hilfsmittel; Polieren; ∅ 23/49 mm",
                "herstellername" => "Omnident",
                "refnummer" => "69852",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "69852",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Ausarbeiten_Polieren/Polierbuersten_unmon/Schmalbuersten+Ziegenhaar/?card=15906",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Modellierwachs Spezial, 500g Sommer 1,5mm ",
                "artikelzusatzinfo" => "Hilfsmittel; Modellieren; Erstarrungspunkt: Medium 55 °C, Sommer 56 °C, Winter 54 °C",
                "herstellername" => "Gebdi",
                "refnummer" => "70811",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "49980",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/Modellieren/Wachse/Modellierwachs+Spezial/?shop_category=&card=11497",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "_AESTHETIC Wax Medium + Hard ",
                "artikelzusatzinfo" => "Hilfsmittel; Modellieren; Farbe 34, Mittlerer Härtegrad, Erstarrungspunkt 80-81°C (Wax Medium) ",
                "herstellername" => "Candulor",
                "refnummer" => "",
                "lieferantname" => "",
                "bestellnummer" => "",
                "url" => "",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Winkelklammer, Gr. 1,  10 Stk",
                "artikelzusatzinfo" => "Klammerdraht; Klammern; einarmig links",
                "herstellername" => "Speiko",
                "refnummer" => "1804",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "71072",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/KFO/Draehte_Buegel/Winkelklammer/?shop_category=&card=166063",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Winkelklammer, Gr. 2, 10 Stk",
                "artikelzusatzinfo" => "Klammerdraht; Klammern; einarmig rechts",
                "herstellername" => "Speiko",
                "refnummer" => "1805",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "71073",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/KFO/Draehte_Buegel/Winkelklammer/?shop_category=&card=166066",
                "val8" => ""
            ),
            array(
                "abteilungname" => "Prothetik",
                "artikelname" => "Zinnfolie auf Rolle, 114x100x0,3mm",
                "artikelzusatzinfo" => "Hilfsmittel; Modellieren; 99,9% Reinzinn, 250g",
                "herstellername" => "Dentaurum",
                "refnummer" => "324-730-00",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "53494",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Praxis/Abformung-ProthetikP/AbformzubehoerP/Zinnfolie+auf+Rolle/?card=141069",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "_SprintRay Pro 95S Tank",
                "artikelzusatzinfo" => "3D-Druck; Zubehör; Resinwanne mit RFID-Chip",
                "herstellername" => "SprintRay",
                "refnummer" => "SRI-0503007",
                "lieferantname" => "SprintRay",
                "bestellnummer" => "SRI-0503007",
                "url" => "PDF",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "_SprintRay Pro 95S optical polish tank ",
                "artikelzusatzinfo" => "3D-Druck; Zubehör; Resinwanne mit RFID-Chip",
                "herstellername" => "SprintRay",
                "refnummer" => "SRI-0503042",
                "lieferantname" => "SprintRay",
                "bestellnummer" => "SRI-0503042",
                "url" => "PDF",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "KeyTray - purple",
                "artikelzusatzinfo" => "3D-Druck; Verbrauch; 3D-Druck-Löffelmaterial, lila",
                "herstellername" => "Keystone Industries",
                "refnummer" => "4220007",
                "lieferantname" => "SprintRay",
                "bestellnummer" => "KEY-4220007",
                "url" => "https://keyprint.keystoneindustries.com/keytray/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "3mm CoCr Fräser für 6mm Schaft (4-Schneider Torus)",
                "artikelzusatzinfo" => "NEM-Fräser; AT; FLATCOAT® TTN Beschichtung mit Messingring (17mm vom Schaftende)",
                "herstellername" => "Alien-Tools",
                "refnummer" => "CT.TTN.T4.30.6.150.45.R.R6",
                "lieferantname" => "Alien-Tools",
                "bestellnummer" => "CT.TTN.T4.30.6.150.45.R.R6",
                "url" => "https://shop.alien-tools.com/de/produkt/ct-ttn-t4-30-6-150-45-r-r6/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "3mm CoCr Fräser für 6mm Schaft (2-Schneider Ball)",
                "artikelzusatzinfo" => "NEM-Fräser; AT; FLATCOAT® TTN Beschichtung mit Messingring (17mm vom Schaftende)",
                "herstellername" => "Alien-Tools",
                "refnummer" => "CT.TTN.B2.30.6.160.50.X.R6",
                "lieferantname" => "Alien-Tools",
                "bestellnummer" => "CT.TTN.B2.30.6.160.50.X.R6",
                "url" => "https://shop.alien-tools.com/de/produkt/ct-ttn-30-6-16-50-r6/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "2mm CoCr Fräser für 6mm Schaft (2-Schneider Ball)",
                "artikelzusatzinfo" => "NEM-Fräser; AT; FLATCOAT® TTN Beschichtung mit Messingring (17mm vom Schaftende)",
                "herstellername" => "Alien-Tools",
                "refnummer" => "CT.TTN.B2.20.6.120.50.X.R6",
                "lieferantname" => "Alien-Tools",
                "bestellnummer" => "CT.TTN.B2.20.6.120.50.X.R6",
                "url" => "https://shop.alien-tools.com/de/produkt/ct-ttn-20-6-12-50-r6/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "1mm CoCr Fräser für 6mm Schaft (2-Schneider Ball)",
                "artikelzusatzinfo" => "NEM-Fräser; AT; FLATCOAT® TTN Beschichtung mit Messingring (17mm vom Schaftende)",
                "herstellername" => "Alien-Tools",
                "refnummer" => "CT.TTN.B2.10.6.090.50.X.R6",
                "lieferantname" => "Alien-Tools",
                "bestellnummer" => "CT.TTN.B2.10.6.090.50.X.R6",
                "url" => "https://shop.alien-tools.com/de/produkt/ct-ttn-10-6-9-50-r6/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "2.5mm Zirkon Fräser für 6mm Schaft (2-Schneider Ball)",
                "artikelzusatzinfo" => "Zirkon-Fräser; AT; FLATCOAT® DIA Beschichtung mit Messingring (17mm vom Schaftende)",
                "herstellername" => "Alien-Tools",
                "refnummer" => "ZT.DIA.B2.25.6.250.53.X.R6",
                "lieferantname" => "Alien-Tools",
                "bestellnummer" => "ZT.DIA.B2.25.6.250.53.X.R6",
                "url" => "https://shop.alien-tools.com/de/produkt/zt-dia-25-6-25-53-r6/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "1mm Zirkon Fräser für 6mm Schaft (2-Schneider Ball)",
                "artikelzusatzinfo" => "Zirkon-Fräser; AT; FLATCOAT® DIA Beschichtung mit Messingring (17mm vom Schaftende)",
                "herstellername" => "Alien-Tools",
                "refnummer" => "ZT.DIA.B2.10.6.150.53.X.R6",
                "lieferantname" => "Alien-Tools",
                "bestellnummer" => "ZT.DIA.B2.10.6.150.53.X.R6",
                "url" => "https://shop.alien-tools.com/de/produkt/zt-dia-10-6-15-53-r6/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "0.6mm Zirkon Fräser für 6mm Schaft (2-Schneider Ball)",
                "artikelzusatzinfo" => "Zirkon-Fräser; AT; FLATCOAT® DIA Beschichtung mit Messingring (17mm vom Schaftende)",
                "herstellername" => "Alien-Tools",
                "refnummer" => "ZT.DIA.B2.06.6.060.53.X.R6 ",
                "lieferantname" => "Alien-Tools",
                "bestellnummer" => "ZT.DIA.B2.06.6.060.53.X.R6 ",
                "url" => "https://shop.alien-tools.com/de/produkt/zt-dia-06-6-06-53-r6/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "2mm PMMA/PEEK Fräser für 6mm Schaft (2-Schneider Ball)",
                "artikelzusatzinfo" => "PMMA-Fräser; AT; FLATCOAT® TEC Beschichtung mit Messingring (17 mm vom Schaftende)",
                "herstellername" => "Alien-Tools",
                "refnummer" => "PT.TEC.B2.20.6.250.53.X.R6",
                "lieferantname" => "Alien-Tools",
                "bestellnummer" => "PT.TEC.B2.20.6.250.53.X.R6",
                "url" => "https://shop.alien-tools.com/de/produkt/pt-tec-20-6-25-53-r6/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "1mm PMMA/PEEK Fräser für 6mm Schaft (2-Schneider Ball)",
                "artikelzusatzinfo" => "PMMA-Fräser; AT; FLATCOAT® TEC Beschichtung mit Messingring (17 mm vom Schaftende)",
                "herstellername" => "Alien-Tools",
                "refnummer" => "PT.TEC.B2.10.6.150.53.X.R6",
                "lieferantname" => "Alien-Tools",
                "bestellnummer" => "PT.TEC.B2.10.6.150.53.X.R6",
                "url" => "https://shop.alien-tools.com/de/produkt/pt-tec-10-6-15-53-r6/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Spezialfräser für Kunststoff mit 6mm Schaft flach ∅3,0mm",
                "artikelzusatzinfo" => "PMMA-Fräser; OCC; unbeschichteter Flachfräser (ohne Messingring)",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "68-1056",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "68-1056",
                "url" => "https://organical-cadcam.com/shop/fraeser-zubehoer/fraeser-fuer-organical-maschinen/fraeser-multiserie-d8s/spezialfraeser-fuer-kunststoff-mit-6mm-schaft/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Cercon Fräser, Cercon, 2,0mm",
                "artikelzusatzinfo" => "Zirkon-Fräser; CB; CerconBrain expert Fräser Kennzahl 0",
                "herstellername" => "Degudent",
                "refnummer" => "5355580102",
                "lieferantname" => "DentsplySirona",
                "bestellnummer" => "5355580102",
                "url" => "https://www.degushop.de/sap(bD1kZSZjPTAwMSZkPW1pbg==)/bc/bsp/sap/zcv_ds_degushop/session_logoff_single.htm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Cercon Fräser, Cercon, 1,0mm",
                "artikelzusatzinfo" => "Zirkon-Fräser; CB; CerconBrain expert Fräser Kennzahl 0",
                "herstellername" => "Degudent",
                "refnummer" => "5355580101",
                "lieferantname" => "DentsplySirona",
                "bestellnummer" => "5355580101",
                "url" => "https://www.degushop.de/sap(bD1kZSZjPTAwMSZkPW1pbg==)/bc/bsp/sap/zcv_ds_degushop/session_logoff_single.htm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Cercon Fräser, Cercon, 0,5mm",
                "artikelzusatzinfo" => "Zirkon-Fräser; CB; CerconBrain expert Fräser Kennzahl 0",
                "herstellername" => "Degudent",
                "refnummer" => "5355580104",
                "lieferantname" => "DentsplySirona",
                "bestellnummer" => "5355580104",
                "url" => "https://www.degushop.de/sap(bD1kZSZjPTAwMSZkPW1pbg==)/bc/bsp/sap/zcv_ds_degushop/session_logoff_single.htm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Cercon Fräser, PMMA, 2,0mm",
                "artikelzusatzinfo" => "PMMA-Fräser; CB; CerconBrain expert Fräser Kennzahl 2",
                "herstellername" => "Degudent",
                "refnummer" => "5355580112",
                "lieferantname" => "DentsplySirona",
                "bestellnummer" => "5355580112",
                "url" => "https://www.degushop.de/sap(bD1kZSZjPTAwMSZkPW1pbg==)/bc/bsp/sap/zcv_ds_degushop/session_logoff_single.htm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Cercon Fräser, PMMA, 1,0mm",
                "artikelzusatzinfo" => "PMMA-Fräser; CB; CerconBrain expert Fräser Kennzahl 2",
                "herstellername" => "Degudent",
                "refnummer" => "5355580111",
                "lieferantname" => "DentsplySirona",
                "bestellnummer" => "5355580111",
                "url" => "https://www.degushop.de/sap(bD1kZSZjPTAwMSZkPW1pbg==)/bc/bsp/sap/zcv_ds_degushop/session_logoff_single.htm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Cercon Fräser, PMMA, 0,5mm",
                "artikelzusatzinfo" => "PMMA-Fräser; CB; CerconBrain expert Fräser Kennzahl 2",
                "herstellername" => "Degudent",
                "refnummer" => "5355580114",
                "lieferantname" => "DentsplySirona",
                "bestellnummer" => "5355580114",
                "url" => "https://www.degushop.de/sap(bD1kZSZjPTAwMSZkPW1pbg==)/bc/bsp/sap/zcv_ds_degushop/session_logoff_single.htm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "G240-R-35",
                "artikelzusatzinfo" => "Glaskeramik-Fräser; VHF; VHF - Glaskeramik Schleifstift-Fräser 2,4mm rund",
                "herstellername" => "vhf camfacture",
                "refnummer" => "G240-R-35",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "68-1207",
                "url" => "https://organical-cadcam.com/shop/fraeser-zubehoer/fraeser-fuer-organical-maschinen/fraeser-desktop-6-7n/g240-r-35/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "G100-R-35",
                "artikelzusatzinfo" => "Glaskeramik-Fräser; VHF; VHF - Glaskeramik Schleifstift-Fräser 1,0mm rund",
                "herstellername" => "vhf camfacture",
                "refnummer" => "G100-R-35",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "68-1201",
                "url" => "https://organical-cadcam.com/shop/fraeser-zubehoer/fraeser-fuer-organical-maschinen/fraeser-desktop-6-7n/g100-r-35/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "G060-R-35",
                "artikelzusatzinfo" => "Glaskeramik-Fräser; VHF; VHF - Glaskeramik Schleifstift-Fräser 0,6mm rund",
                "herstellername" => "vhf camfacture",
                "refnummer" => "G060-R-35",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "68-1200",
                "url" => "https://organical-cadcam.com/shop/fraeser-zubehoer/fraeser-fuer-organical-maschinen/fraeser-desktop-6-7n/g060-r-35/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "G120-T-35",
                "artikelzusatzinfo" => "Glaskeramik-Fräser; VHF; VHF - Glaskeramik Schleifstift-Fräser 1,2mm torisch",
                "herstellername" => "vhf camfacture",
                "refnummer" => "G120-T-35",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "68-1203",
                "url" => "https://organical-cadcam.com/shop/fraeser-zubehoer/fraeser-fuer-organical-maschinen/fraeser-desktop-6-7n/g060-t-35/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "G060-T-35",
                "artikelzusatzinfo" => "Glaskeramik-Fräser; VHF; VHF - Glaskeramik Schleifstift-Fräser 0,6mm torisch",
                "herstellername" => "vhf camfacture",
                "refnummer" => "G060-T-35",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "68-1202",
                "url" => "https://organical-cadcam.com/shop/fraeser-zubehoer/fraeser-fuer-organical-maschinen/fraeser-desktop-6-7n/g060-t-35-2/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "M200-R4-32",
                "artikelzusatzinfo" => "NEM-Fräser; VHF; VHF - NEM&Titan-Fräser Länge 32mm(!) 2mm, 2-Zahn-Radius",
                "herstellername" => "vhf camfacture",
                "refnummer" => "M200-R4-32",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "68-1095",
                "url" => "https://organical-cadcam.com/shop/fraeser-zubehoer/fraeser-fuer-organical-maschinen/fuer-desktop-1-3-4life-4life/m200-r4-32/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "M100-R2-32",
                "artikelzusatzinfo" => "NEM-Fräser; VHF; VHF - NEM&Titan-Fräser Länge 32mm(!) 1mm, 2-Zahn-Radius",
                "herstellername" => "vhf camfacture",
                "refnummer" => "M100-R2-32",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "68-1092",
                "url" => "https://organical-cadcam.com/shop/fraeser-zubehoer/fraeser-fuer-organical-maschinen/fuer-desktop-1-3-4life-4life/m100-r2-32/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "M060-R2-32",
                "artikelzusatzinfo" => "NEM-Fräser; VHF; VHF - NEM&Titan-Fräser Länge 32mm(!) 0,6mm, 2-Zahn-Radius",
                "herstellername" => "vhf camfacture",
                "refnummer" => "M060-R2-32",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "68-1091",
                "url" => "https://organical-cadcam.com/shop/fraeser-zubehoer/fraeser-fuer-organical-maschinen/fuer-desktop-1-3-4life-4life/m060-r2-32/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "H8 mm - Organic CoCr ( 2022 )",
                "artikelzusatzinfo" => "NEM-Rohlinge; OCC; Sonder-Rohling mit geringerer Höhe als Standard-10mm-Ronde ohne Nut",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "67-2130",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "67-2130",
                "url" => "https://organical-cadcam.com/shop/fraesrohlinge/metall/organic-cocr-2022/?attribute_hoehen=H8+mm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Argen CoCr 255 - 98 mm, 10 mm",
                "artikelzusatzinfo" => "NEM-Rohlinge; Argen; NEM-Ronde ohne Stufe",
                "herstellername" => "Argen Dental",
                "refnummer" => "47900",
                "lieferantname" => "Argen Dental",
                "bestellnummer" => "47900",
                "url" => "https://www.argen.de/produkt/argen-discs/cocr-discs/argen-cocr-255/?attribute_durchmesser=98+mm&attribute_hoehe=10+mm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Argen CoCr 255 - 98 mm, 12 mm",
                "artikelzusatzinfo" => "NEM-Rohlinge; Argen; NEM-Ronde mit Stufe",
                "herstellername" => "Argen Dental",
                "refnummer" => "47901",
                "lieferantname" => "Argen Dental",
                "bestellnummer" => "47901",
                "url" => "https://www.argen.de/produkt/argen-discs/cocr-discs/argen-cocr-255/?attribute_durchmesser=98+mm&attribute_hoehe=12+mm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Argen CoCr 255 - 98 mm, 13,5 mm",
                "artikelzusatzinfo" => "NEM-Rohlinge; Argen; NEM-Ronde mit Stufe",
                "herstellername" => "Argen Dental",
                "refnummer" => "47902",
                "lieferantname" => "Argen Dental",
                "bestellnummer" => "47902",
                "url" => "https://www.argen.de/produkt/argen-discs/cocr-discs/argen-cocr-255/?attribute_durchmesser=98+mm&attribute_hoehe=13%2C5+mm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Argen CoCr 255 - 98 mm, 15 mm",
                "artikelzusatzinfo" => "NEM-Rohlinge; Argen; NEM-Ronde mit Stufe",
                "herstellername" => "Argen Dental",
                "refnummer" => "47903",
                "lieferantname" => "Argen Dental",
                "bestellnummer" => "47903",
                "url" => "https://www.argen.de/produkt/argen-discs/cocr-discs/argen-cocr-255/?attribute_durchmesser=98+mm&attribute_hoehe=15+mm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Argen CoCr 255 - 98 mm, 18 mm",
                "artikelzusatzinfo" => "NEM-Rohlinge; Argen; NEM-Ronde mit Stufe",
                "herstellername" => "Argen Dental",
                "refnummer" => "47904",
                "lieferantname" => "Argen Dental",
                "bestellnummer" => "47904",
                "url" => "https://www.argen.de/produkt/argen-discs/cocr-discs/argen-cocr-255/?attribute_durchmesser=98+mm&attribute_hoehe=18+mm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Argen CoCr 255 - 98 mm, 20 mm",
                "artikelzusatzinfo" => "NEM-Rohlinge; Argen; NEM-Ronde mit Stufe",
                "herstellername" => "Argen Dental",
                "refnummer" => "47905",
                "lieferantname" => "Argen Dental",
                "bestellnummer" => "47905",
                "url" => "https://www.argen.de/produkt/argen-discs/cocr-discs/argen-cocr-255/?attribute_durchmesser=98+mm&attribute_hoehe=20+mm",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Organic PMMA ECO Clear H20 mm mit Nut",
                "artikelzusatzinfo" => "PMMA-Rohlinge; OCC; klare Kunststoff-Ronde mit Standard-Durchmesser 98,5mm",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "67-3803",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "67-3803",
                "url" => "https://organical-cadcam.com/shop/fraesrohlinge/kunststoffe/organic-pmma-eco-clear/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Organic PMMA ECO Clear Ø120mm H20 mit Nut",
                "artikelzusatzinfo" => "PMMA-Rohlinge; OCC; klare Kunststoff-Ronde mit größerem Durchmesser als Standard",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "67-3804",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "67-3804",
                "url" => "https://organical-cadcam.com/shop/fraesrohlinge/kunststoffe/organic-pmma-eco-clear-oe120mm/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Organic PMMA ECO Colour Berry H20 mm mit Nut",
                "artikelzusatzinfo" => "PMMA-Rohlinge; OCC; Rot-Ton-Ronde mit Standard-Durchmesser und Stufe",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "67-3813",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "67-3813",
                "url" => "https://organical-cadcam.com/shop/fraesrohlinge/kunststoffe/organic-pmma-eco-colour/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Organic PMMA ECO Colour Blue H20 mm mit Nut",
                "artikelzusatzinfo" => "PMMA-Rohlinge; OCC; Blau-Ton-Ronde mit Standard-Durchmesser und Stufe",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "67-3810",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "67-3810",
                "url" => "https://organical-cadcam.com/shop/fraesrohlinge/kunststoffe/organic-pmma-eco-colour/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Organic PMMA ECO Colour Green H20 mm mit Nut",
                "artikelzusatzinfo" => "PMMA-Rohlinge; OCC; Grün-Ton-Ronde mit Standard-Durchmesser und Stufe",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "67-3811",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "67-3811",
                "url" => "https://organical-cadcam.com/shop/fraesrohlinge/kunststoffe/organic-pmma-eco-colour/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Organic PMMA ECO Colour Orange H20 mm mit Nut",
                "artikelzusatzinfo" => "PMMA-Rohlinge; OCC; Gelbrot-Ton-Ronde mit Standard-Durchmesser und Stufe",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "67-3812",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "67-3812",
                "url" => "https://organical-cadcam.com/shop/fraesrohlinge/kunststoffe/organic-pmma-eco-colour/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Organic PMMA ECO Multi Colour fünfschichtig H20 mm mit Nut A1",
                "artikelzusatzinfo" => "PMMA-Rohlinge; OCC; eingefärbte Vita A1 mit Standard-Durchmesser und Stufe",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "67-3302",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "67-3302",
                "url" => "https://organical-cadcam.com/shop/fraesrohlinge/kunststoffe/organic-pmma-eco-multi-colour-fuenfschichtig/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Organic PMMA ECO Multi Colour fünfschichtig H20 mm mit Nut A2",
                "artikelzusatzinfo" => "PMMA-Rohlinge; OCC; eingefärbte Vita A1 mit Standard-Durchmesser und Stufe",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "67-3305",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "67-3305",
                "url" => "https://organical-cadcam.com/shop/fraesrohlinge/kunststoffe/organic-pmma-eco-multi-colour-fuenfschichtig/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Organic PMMA ECO Multi Colour fünfschichtig H20 mm mit Nut A3",
                "artikelzusatzinfo" => "PMMA-Rohlinge; OCC; eingefärbte Vita A1 mit Standard-Durchmesser und Stufe",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "67-3308",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "67-3308",
                "url" => "https://organical-cadcam.com/shop/fraesrohlinge/kunststoffe/organic-pmma-eco-multi-colour-fuenfschichtig/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "Organic PMMA ECO Multi Colour fünfschichtig H20 mm mit Nut B1",
                "artikelzusatzinfo" => "PMMA-Rohlinge; OCC; eingefärbte Vita A1 mit Standard-Durchmesser und Stufe",
                "herstellername" => "Organical CAD CAM",
                "refnummer" => "67-3311",
                "lieferantname" => "Organical CAD CAM",
                "bestellnummer" => "67-3311",
                "url" => "https://organical-cadcam.com/shop/fraesrohlinge/kunststoffe/organic-pmma-eco-multi-colour-fuenfschichtig/",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "BioStar Ø 98,5mm - H 18mm - elfenbein ",
                "artikelzusatzinfo" => "PMMA-Rohlinge; EH; sandfarbene Ronde mit Sonderhöhe 18mm(!) und Stufe",
                "herstellername" => "Ernst Hinrichs",
                "refnummer" => "550059",
                "lieferantname" => "van der Ven",
                "bestellnummer" => "104763",
                "url" => "https://www.vshop.de/shop/de/shop/Verbrauchsmaterial/Laborbedarf/CAD-CAML/Cerec-RondenL/PMMA+BioStar+%C3%98+98+5mm+-+H+18mm/?card=111245",
                "val8" => ""
            ),
            array(
                "abteilungname" => "CADCAM",
                "artikelname" => "optimill memosplint 20mm x 98,5mm mit Stufe",
                "artikelzusatzinfo" => "PMMA-Rohlinge; D; klare thermoelastische Ronde ",
                "herstellername" => "dentona®",
                "refnummer" => " 42243",
                "lieferantname" => "dentona",
                "bestellnummer" => "42243",
                "url" => "https://dentona.de/optimill/kunststoff-ronden/optimill-memosplint/42243",
                "val8" => ""
            )
        );

        $this->saveArtikelListe($artikelNamen);
    }

    private function saveArtikelListe($artikelNamen)
    {
        $artikels = [];
        foreach ($artikelNamen as $artikelData) {
            $departmentname = $artikelData['abteilungname'];
            $department = $this->departmentRepository->findOneBy(['name' => $departmentname]);

            if ($department === null && DepartmentTyp::getByName($departmentname) !== null) {
                $department = new Department();
                $department->setName($departmentname)
                    ->setTyp(DepartmentTyp::getByName($departmentname)->value);

                $this->departmentRepository->save($department);
            }


            $artikelName = trim($artikelData['artikelname']);
            $artikelZusatzInfo = trim($artikelData['artikelzusatzinfo']) ?? null;
            $url = trim($artikelData['url']) ?? null;

            $artikel = $this->artikelRepository->findOneBy(['name' => $artikelName]);

            if ($artikel === null) {
                $artikel = new Artikel();
            }

            $artikel->setName($artikelName)
                ->setDescription($artikelZusatzInfo)
                ->setUrl($url)
                ->addDepartment($department);

            $this->artikelRepository->save($artikel);

            $herstellerName = trim($artikelData['herstellername']);

            $refnummer = trim($artikelData['refnummer']) ?? null;
            $lieferName = trim($artikelData['lieferantname']) ?? null;
            $bestellnummer = trim($artikelData['bestellnummer']) ?? null;

            if (!empty($herstellerName)) {
                $hersteller = $this->herstellerRepository->findOneBy(['name' => $herstellerName]);

                if ($hersteller === null) {
                    $hersteller = new Hersteller();
                    $hersteller->setName($herstellerName);

                    $this->herstellerRepository->save($hersteller);
                }

                if ($hersteller !== null) {
                    $artikel->addHersteller($hersteller);
                }

                if ($hersteller !== null && $refnummer !== null) {
                    $existingArtikelToHersRefn = $this->artikelToHerstellerRefnummerRepository->findOneBy(
                        [
                            'refnummer' => $refnummer,
                            'hersteller' => $hersteller,
                            'artikel' => $artikel
                        ]
                    );

                    if ($existingArtikelToHersRefn === null) {
                        $artikelToHersRefNummer = new ArtikelToHerstRefnummer();

                        $artikelToHersRefNummer->setHersteller($hersteller)
                            ->setRefnummer($refnummer)
                            ->setArtikel($artikel);
                    }
                }
            }

            if (!empty($lieferName)) {
                $lieferant = $this->lieferantRepository->findOneBy(['name' => $lieferName]);

                if ($lieferant === null) {
                    $lieferant = new Lieferant();
                    $lieferant->setName($lieferName);
                    $this->lieferantRepository->save($lieferant);
                }

                if ($lieferant !== null) {
                    $artikel->addLieferant($lieferant);
                }

                if ($lieferant !== null && $bestellnummer !== null) {
                    $existingArtikelToLiefBn = $this->artikelToLieferantBestellnummerRepository->findOneBy(
                        [
                            'bestellnummer' => $bestellnummer,
                            'lieferant' => $lieferant,
                            'artikel' => $artikel
                        ]
                    );

                    if ($existingArtikelToLiefBn === null) {
                        $artikelToLieferantBestellnummer = new ArtikelToLieferBestellnummer();
                        $artikelToLieferantBestellnummer->setLieferant($lieferant)
                            ->setArtikel($artikel)
                            ->setBestellnummer($bestellnummer);

                        $artikel->addArtikelToLieferantBestellnummer($artikelToLieferantBestellnummer);
                    }
                }
            }


            $artikels[] = $artikel;
        }

        $this->artikelRepository->saveAll($artikels);
    }

    private function createMitarbeiter()
    {
        $users = [
            [
                'mitarbeiterId' => 1,
                'vorname' => 'Sandra',
                'nachname' => 'Michels'
            ],
            [
                'mitarbeiterId' => 10,
                'vorname' => 'Daniela',
                'nachname' => 'Rösler'
            ],
            [
                'mitarbeiterId' => 100,
                'vorname' => 'Kyra',
                'nachname' => 'Michels'
            ],
            [
                'mitarbeiterId' => 13,
                'vorname' => 'Susanne',
                'nachname' => 'Ste'
            ],
            [
                'mitarbeiterId' => 2,
                'vorname' => 'Tanja',
                'nachname' => 'Mei'
            ],
            [
                'mitarbeiterId' => 20,
                'vorname' => 'Hanna Lea',
                'nachname' => 'Bie'
            ],
            [
                'mitarbeiterId' => 28,
                'vorname' => 'Alev',
                'nachname' => 'Dur'
            ],
            [
                'mitarbeiterId' => 32,
                'vorname' => 'Markus',
                'nachname' => 'Kun'
            ],
            [
                'mitarbeiterId' => 45,
                'vorname' => 'Stefan',
                'nachname' => 'Mic'
            ],
            [
                'mitarbeiterId' => 5,
                'vorname' => 'Andre',
                'nachname' => 'Sei'
            ],
            [
                'mitarbeiterId' => 55,
                'vorname' => 'Monika',
                'nachname' => 'Lüt'
            ],
            [
                'mitarbeiterId' => 66,
                'vorname' => 'Jonny',
                'nachname' => 'Tre'
            ],
            [
                'mitarbeiterId' => 7,
                'vorname' => 'Bianka',
                'nachname' => 'Dav'
            ],
            [
                'mitarbeiterId' => 72,
                'vorname' => 'Stephanie',
                'nachname' => 'Hof'
            ],
            [
                'mitarbeiterId' => 75,
                'vorname' => 'Nabi',
                'nachname' => 'Haj'
            ],
            [
                'mitarbeiterId' => 79,
                'vorname' => 'Roman',
                'nachname' => 'Wei'
            ],
            [
                'mitarbeiterId' => 88,
                'vorname' => 'Ulrike',
                'nachname' => 'Dav'
            ],
            [
                'mitarbeiterId' => 90,
                'vorname' => 'Sarah',
                'nachname' => 'Cap'
            ],

        ];

        foreach ($users as $userArrayItem) {
            $user = $this->userRepository->findOneBy(['mitarbeiterId' => (int)$userArrayItem['mitarbeiterId']]);

            if ($user === null) {
                $user = new User();
            }

            $user->setMitarbeiterId((int)$userArrayItem['mitarbeiterId'])
                ->setFirstname($userArrayItem['vorname'])
                ->setLastname($userArrayItem['nachname'])
                ->setEmail($userArrayItem['vorname'] . '.' . $userArrayItem['nachname'] . '@test.de')
                ->setCreatedAt(new \DateTime())
                ->setUpdatedAt(new \DateTime());

            $this->userRepository->save($user);

            $mitarbeiter = $this->mitarbeiterRepository->createQueryBuilder('m')
                ->leftJoin('m.user', 'u')
                ->where('u.mitarbeiterId = :mitarbeiterId')
                ->setParameter('mitarbeiterId', $userArrayItem['mitarbeiterId'])
                ->getQuery()
                ->getOneOrNullResult();

            if ($mitarbeiter === null) {
                $mitarbeiter = new Mitarbeiter();
            }

            $mitarbeiter->setUser($user);
            $mitarbeiter->setVorname($userArrayItem['vorname']);
            $mitarbeiter->setNachname($userArrayItem['nachname']);

            $this->mitarbeiterRepository->save($mitarbeiter);
        }
    }
}