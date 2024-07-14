<?php

namespace App\Command\Entity;

use App\Entity\Department;
use App\Entity\DepartmentTyp;
use App\Entity\Material\Artikel;
use App\Entity\Mitarbeiter;
use App\Entity\User;
use App\Repository\DepartmentRepository;
use App\Repository\Material\ArtikelRepository;
use App\Repository\MitarbeiterRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
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
        private readonly MitarbeiterRepository $mitarbeiterRepository
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
            $this->generateArtikelForArbeitsvorbereitung();
            $this->generateArtikelForEdelmetall();
            $this->generateArtikelForKunstoff();
            $this->generateArtikelForCadCam();
            $this->createMitarbeiter();
        } catch (\Exception $e) {
            throw new \Exception('Es ist ein Fehler aufgetreten: ' . $e->getMessage());
        }

        $io->success('Artikel wurden erfolgreich erstellt.');

        return Command::SUCCESS;
    }

    private function generateArtikelForArbeitsvorbereitung()
    {
        $artikelNamen = [
            'Alginat Image',
            'Algitray 1kg oder flüssig 5L (Zhermack)',
            'Artiplatten System Adesso rund Mälzer',
            'Artiplatten System Splitex  eckig Mälzer 10183',
            'Artigator Modellplatte blau 50 Stk',
            'Coppie Flux (Dentona)',
            'dento-sol  Isoliermittel 1000ml (Dentona)',
            'Diverse Fräsen',
            'Bohrer HM',
            'HS-Dowel Pins Gr. 1',
            'HS-Dowel Pins Gr.2',
            'Dreve Dosper Mischkanülen',
            'Filterbeutel Tischabsaugung Omnident',
            'Filter für Ausbrüher',
            'Filterbeutel Trimmer',
            'Flüssigseife Salina 5L',
            'Flüssigseife Nachfüllpack klein/Spenderflasche klein',
            'Gypsitray 2L',
            'Artibase weiß',
            'dento-stone220weiß 25kg',
            'esthetic-base 300 goldbraun 25 kg',
            'sockel-plaster GT',
            'Händedesinfektion (Zhermack Zeta 66) 500ml',
            'Händedesinfektion OpSept (TMS) 500ml',
            'Haftplatten',
            'Handschuhe Nitril puderfrei',
            'Instruprint Desinfektion (TMS) NEU: Orbi-Sept Abformdesinfektion (Art.Nr. 262558 bei Multident)',
            'Knete (Spielwarengeschäft)',
            'Knete Putty Nr. 140 70',
            'Kompakt-Pins nr. 101 72 K',
            'Küchenrolle',
            'Magnettöpfe',
            'Magnete (zu den Töpfen)',
            'Mirapont Agent Plus',
            'Pinbohrer Dentona Nr. 36030',
            'Pins konisch Nr',
            'Retentionsscheiben',
            'Schleifbänder KaVo 10 Stk.',
            'Schleifband-Träger 15100',
            'Schleifbänder fein 15100-120',
            'Schleifbänder grob 15100- 80',
            'Schleifkappen groß 9958R 000 130',
            'Schutzscheibe',
            'Sekundenkleber Omnident',
            'Sockelplatten Nr. 140 30 GR grau/klein',
            'Sockelplatten Nr. 140 40 GR grau/groß',
            'Splitcastplatten Nr. 140 50 klein',
            'Splitcastplatten Nr. 140 60 groß',
            'Sprühdesinfektion',
            'Temdent classic Pulver hell 100gr',
            'Temdent flüssig 100 ml',
            'Trennscheibe f. Sägemod.',
            'Ultraschall Polierpastenreiniger (flüssig) Siladent'
        ];

        $this->saveArtikelListe(DepartmentTyp::ARBEITSVORBEREITUNG, $artikelNamen);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    private function generateArtikelForEdelmetall()
    {
        $artikelNamen = [
            'Aceton',
            'AGC Cem (Wieland-675262)  G-Cem Link Ace Automix A2',
            'Anti-Flußmittel/Anti-flux (Contex) 25240004',
            'Bisonbürsten von Renfert No.766 11 00   Durchm18mm, 100 Stk. (76280)',
            'Destilliertes Wasser',
            'Dubliersilikon Brasil 22',
            'Easyform LC gel           Diagel LC',
            'Einbettmasse Fuji II                  Platinum',
            'Einbettmasse Fuji II Liquid      Platinum',
            'Einbettmasse Shera Frixion',
            'Einbettmasse Shera Frixion Flüssigkeit',
            'Einbettmasse Press Vest Premium 5kg',
            'Einbettmasse Press Vest Premium 1L Flüssigkeit',
            'Entspanner-Spray    DFS Diamon',
            'Fegupol 8059 (40g)',
            'Flussmittelpaste, rot f. EM-Leg.',
            'Gusstiegel',
            'Handschuhe',
            'Klebeverbinder/Matrize, Patrize, Winkel, …',
            'Mundschutz (von HS, grün, mit Gummi) Art.Nr.9006147',
            'Mischkanülen (Dreve-49601)',
            'Muffel Vlies (Omnident)',
            'Muffel-Ringe Gr.6',
            'NEM ADT CC-280',
            'NEM Lot (Dentaurum 12-306-00)',
            'Okklupapier (Farbe)',
            'Oscar Diamand Polish',
            'Papiertücher',
            'Pattern Resin',
            'Polierbürsten (Gr./Bezeichnung)',
            'Polierpaste     (Gr./Bezeichnung)',
            'Press Invex Liquid (Ref.597064)',
            'Rema-Flux1 102-304-00',
            'Retentionsperlen o. Flüssigkeit (Kleber) Shofu',
            'Scanspray (Diasol Spray weiß 26-1001) (Okklu-Spray)',
            'Spezial-Flussmittel Oxynon 5325310004',
            'Strahlmittel “Cera Blast” 25kg  50µm',
            'Strahlmittel “Alustral” 25kg  110µm',
            'Strahlmittel “Alustral” 25kg  250µm',
            'Stumpflack',
            'Stahlgold Lot 4g (C.Hafner)',
            'CH-Universal Lot 3g (C.Hafner)',
            'MIG-O-MAT Düsensatz 1.0 (g10) x10  Art.Nr. 50.25019100 für Löt-Gerät',
            'SprintRay Die & Model Gray II Art.Nr. SRI-0202011  1L 3D-Modellmaterial',
            'KeySplint Hard transparent  Art.Nr. KEY-42200004 1kg hartes Schienenmaterial',
            'KeyTray purple Art.Nr. KEY-42200007 1kg lila Löffelmaterial/Bissn.',
            'KeyMask pink Art.Nr. KEY-42000002 0,5kg pink Gingivamaskenmat.'
        ];

        $this->saveArtikelListe(DepartmentTyp::EDELMETALL, $artikelNamen);
    }

    private function generateArtikelForKunstoff()
    {
        $artikelNamen = [
            'Alkohol Isopropanlol 99,9%',
            'Med.Alcohol (Apotheke) 70%',
            'Aceton',
            'Anaxdent Matrix Form 60',
            'Anaxdent Acryline powder clear',
            'Anaxdent Acryline powder x-ray DVT',
            'Anaxdent Acryline liquid',
            'Anaxdent new ouline powder',
            'Anaxdent new ouline liquid',
            'Isopropyl 100 ml',
            '(Aislar Isolierung) 500ml',
            'Anaxdent Flow',
            'Bimbssteinpulver 25kg fein',
            'Bimsteindesinfektion „Bims-Sep“ 5L',
            'Ceramage Dentin Body',
            'Ceramage Dentin Body',
            'Ceramage Dentin Body',
            'Ceramage Gum L (PN1959) (Zahnfleischmasse)',
            'Ceramage Up Gum Color Gum L (PN2391)',
            'Ceramage Up Incisal    Herst.Nr.2358',
            'Ceramage Schneide Incisal',
            'Ceramage Up Body',
            'Ceramage Modeling liquid',
            'Ceramage Universal Pre-Opaque',
            'Ceramage Universal opaquer',
            'Candulor C-plast monomer',
            'Candulor c-plast polymer',
            'Coltex mix medium',
            'Coltene adhesive',
            'Compo+ 40g',
            'Doppelarm-N-Klammer 0,9 REF 1009.1 (Scheu)',
            'Doubliersilikon Brasil',
            'Drahtnetz, vergoldet 2222100 (Renfert)',
            'Dura-Polish Dia 5g',
            'Einmal-Spritzen 20 ml',
            'Erkodent Erkoloc-pro 595420',
            'Erkodent Erko-dur 521220',
            'Erko-flex 584220',
            'Erkodent Füllgranulat 110852',
            'Erkolen Durchm.120mm/2mm',
            'Filterbeutel KaVO Einzelplatzabsaugung',
            'Filzkegel (Hochglanz-Polierbüsten alle Mat.)',
            'GC Optiglace color 5ml Clear',
            'GC Optiglace color 2,6 ml B plus',
            'GC Optiglace color 2,6 ml blau',
            'Handcreme (TMS)',
            'Handschuhe Nitril (TMS)',
            'Hochglanzpolitur',
            'Hot sticks',
            'ISO-C',
            'J-Klammer schräg D 0,9mm 10 Stk. ScheuDental',
            'J-Klammer D 0,9mm 10 Stk. ScheuDental',
            'Menzanium Kl.-Draht 0,7mm/0,8mm/0,9mm…MENZANIUM Spulendraht federhart ScheuDental',
            'Klammer Anker 0,8mm',
            'Knetsilikon 1:1 gum    Dentona 16056',
            'Knetsilikon Supersil 1:1  Dental Kontor 5kg',
            'KMG Candulor Hochglanzpoliermittel',
            'Kosmetiktücher',
            'Lederschwabbel Polirapid  D90mm',
            'LITE ART Color Paste (Shofu) AS PN1974',
            'LITE ART Color Paste               BS PN1975',
            'Löffelplatten (Briegel) Pink C34',
            'Mega Cryl N (Pulver rosa/pink) Xthetic Prime pinkC34',
            'Mega Cryl S+N                        “         Flüssigkeit',
            'Universal Primer',
            'Optiprint tray (3D-Druck) Farbe:orange',
            'Organic 3D Tra (türkis) 1000g  von R&K',
            'OVS II. Opaker Dentsply   Intensiv-Opaker',
            'OVS II. Opaker Verdünner',
            'Papiertücher H3',
            'Papiertücher H2',
            'Polierbürste groß (Calibris)',
            'Polierbürste klein (Slimbürsten) 788 1000',
            'Polierbürste Ziegenhaar',
            'Q-Tips',
            'Schmirgelpapier/Edelkorundpapier 120 oder 240',
            'Seife Kanister',
            'Sekundenkleber',
            'Sheratray Pulver',
            'Sheratray Flüssigkeit',
            'Spüli',
            'Wachsplatten (Anutex)',
            'Wachsplatten soft Candulor (662466) 500g',
            'Wachswälle rot Gebdi',
            'Watte',
            'Winkelklammer einarmig links',
            'Winkelklammer einarmig rechts (Speiko)',
            'Zahnbürste',
            'Zinnfolie auf Rolle'
        ];

        $this->saveArtikelListe(DepartmentTyp::KUNSTSTOFF, $artikelNamen);
    }

    private function generateArtikelForCadCam()
    {
        $artikelNamen = [
            '3 mm flach 68-1026',
            '3 mm 68-1043',
            '2 mm 68-1042',
            '1 mm 68-1041',
            '0,6 mm 68-1040',
            '2,5 mm 68-1004',
            '1,0 mm 68-1003',
            '0,6 mm 68-1005',
            '0,15 mm 68-0010',
            '3 mm lang 68-1049',
            '2 mm 68-1047',
            '1 mm 68-1046',
            '1 mm lang 68-1008',
            '0,6 mm 68-1007',
            '8 mm 67-2108',
            '10 mm 67-2100',
            '12 mm 67-2101',
            '13,5 mm 67-2102',
            '15 mm 67-2103',
            '18 mm 67-2118',
            '20 mm 67-2120',
            'Katana ZR UTML A1 Collar/T:14mm 67-4038',
            'Katana ZR UTML A1 Collar/T:18mm 67-4039',
            'Katana ZR UTML A2 Collar/T:14mm 67 4042',
            'Katana ZR UTML A2 Collar/T:18mm 67-4043',
            'Katana ZR UTML A3 Collar/T:14mm 67-4046',
            'Katana ZR UTML A3 Collar/T:18mm 67-4047',
            'Katana ZR UTML A3,5 Collar/T:14mm 67-4050',
            'Katana ZR UTML A3,5 Collar/T:18mm 67-4051',
            'Katana ZR UTML A4 Collar/T:14mm 67-4054',
            'Katana ZR UTML A4 Collar/T:18mm 67-4055',
            'Katana ZR STML A1 Collar/T:14mm 67-4002',
            'Katana ZR STML A1 Collar/T:18mm 67-4003',
            'Katana ZR STML A1 Collar/T:22mm 67-4004',
            'Katana ZR STML A2 Collar/T:14mm 67-4006',
            'Katana ZR STML A2 Collar/T:18mm 67-4007',
            'Katana ZR STML A2 Collar/T: 22mm 67-4008',
            'Katana ZR STML A3 Collar/T: 14mm 67-4010',
            'Katana ZR STML A3 Collar/T: 18mm 67-4011',
            'Katana ZR STML A3 Collar/T: 22mm 67-4012',
            'Katana ZR STML A3,5 Collar/T: 14mm 67-4014',
            'Katana ZR STML A3,5 Collar/T: 18mm 67-4015',
            'Katana ZR STML A3,5 Collar/T: 22mm 67-4016',
            'Katana ZR ML A White Collar/T: 14mm 67-4414',
            'Katana ZR ML A White Collar/T: 18mm 67-4418',
            'Katana ZR ML A White Collar/T: 22mm 67-4422',
            'Katana ZR ML A Light Collar/T: 14mm 67-4114',
            'Katana ZR ML A Light Collar/T: 18mm 67-4118',
            'Katana ZR ML A Light Collar/T: 22mm 67-4122',
            'Katana ZR ML A Dark Collar/T: 14mm 67-4214',
            'Katana ZR ML A Dark Collar/T: 18mm 67-4218',
            'Katana ZR ML A Dark Collar/T: 22mm 67-4222',
            'Katana ZR ML B Light Collar/T: 14mm 67-4314',
            'Katana ZR ML B Light Collar/T: 18mm 67-4318',
            'Katana ZR ML B Light Collar/T: 22mm 67-4322',
            'ZirCAD Prime von Ivoclar Organic-Zirkon opak light T14mm 67-1414',
            'Organic-Zirkon opak light T18mm 67-1418',
            'Organic-Zirkon opak medium T14mm 67-1514',
            'Organic-Zirkon opak medium T18mm 67-1518',
            'Organic-Zirkon opak dark T14mm 67-1614',
            'Organic-Zirkon opak dark T18mm 67-1618',
            'Organic-PMMA Rohling D98,00mm Organic PMMA-clear T20,0mm 67-3803',
            'Organic PMMA-clear Ø120mmT20,0mm 67-3804',
            'Organic-PMMA-colour A2 T20,0mm 67-3800',
            'Organic-PMMA-colour A3 T20,0mm 67-3801',
            'Organic-PMMA-colour B1 T20,0mm 67-3802',
            'Organic PMMA-colour pink T20,0mm 67-3805',
            'Organic PMMA-colour blue T20,0mm 67-3810',
            'Organic PMMA-colour green T20,0mm 67-3811',
            'Organic PMMA-colour orange T20,0mm 67-3812',
            'Organic PMMA-colour berry T20,0mm 67-3813',
            'Ernst Hinrichs PMMA-Rohling D98,00mm BioStar 18mm elfenbein',
            'Dentona Thermoplast-Rohling D98,50mm Optimill 16mm Memosplint Art.-Nr. 42252'
        ];

        $this->saveArtikelListe(DepartmentTyp::CADCAM, $artikelNamen);
    }

    private function saveArtikelListe(DepartmentTyp $departmentTyp, $artikelNamen)
    {
        $department = $this->departmentRepository->findOneBy(['typ' => $departmentTyp->value]);

        if ($department === null) {
            $department = new Department();
            $department->setName(DepartmentTyp::from($departmentTyp->value)->getName())
                ->setTyp($departmentTyp->value);

            $this->departmentRepository->save($department);
        }

        $artikels = [];
        foreach ($artikelNamen as $artikelName) {
            $artikelName = trim($artikelName);

            $artikel = $this->artikelRepository->findOneBy(['name' => $artikelName]);

            if ($artikel === null) {
                $artikel = new Artikel();
            }

            $artikel->setName($artikelName)
                ->addDepartment($department);

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
                'nachname' => 'Mic'
            ],
            [
                'mitarbeiterId' => 10,
                'vorname' => 'Daniela',
                'nachname' => 'Rös'
            ],
            [
                'mitarbeiterId' => 100,
                'vorname' => 'Kyra',
                'nachname' => 'Mic'
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
                'mitarbeiterId' => 72,
                'vorname' => 'Stephanie',
                'nachname' => 'Hof'
            ],
            [
                'mitarbeiterId' => 75,
                'vorname' => 'Nabi',
                'nachname' => 'Hai'
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
                ->setLastname($userArrayItem['nachname']);

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