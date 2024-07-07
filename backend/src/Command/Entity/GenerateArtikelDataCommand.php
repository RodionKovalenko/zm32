<?php

namespace App\Command\Entity;

use App\Entity\Material\Artikel;
use App\Repository\Material\ArtikelRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateArtikelDataCommand extends Command
{
    protected static $defaultName = 'zm:generate-artikels';
    protected static $defaultDescription = 'Erstellt die default Artikel.';

    public function __construct(private readonly ArtikelRepository $artikelRepository)
    {
        parent::__construct();
    }

    /**
     * @throws \InvalidArgumentException
     */
    protected function configure()
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->generateArtikelForArbeitsvorbereitung();
            $this->generateArtikelForEdelmetall();
            $this->generateArtikelForKunstoff();
            $this->generateArtikelForCadCam();
        } catch (\Exception $e) {
            throw new \Exception('Es ist ein Fehler aufgetreten: ' . $e->getMessage());
        }

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
            'Pins konisch Nr.',
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
            'Ultraschall Polierpastenreiniger (flüssig) Siladent 1kg/1L REF251021 ca.35,-€'
        ];

        $artikels = [];
        foreach ($artikelNamen as $artikelNaman) {
            $artikel = new Artikel();
            $artikel->setName($artikelNaman);

            $this->artikelRepository->save($artikel);
        }
    }

    private function generateArtikelForEdelmetall()
    {
    }

    private function generateArtikelForKunstoff()
    {
    }

    private function generateArtikelForCadCam()
    {
    }
}