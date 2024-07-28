<?php

namespace App\Entity\Material;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'artikel_to_herst_refnummer')]
class ArtikelToHerstRefnummer
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Artikel::class)]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false, onDelete: 'restrict')]
    private ?Artikel $artikel = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Hersteller::class)]
    #[ORM\JoinColumn(name: 'hersteller_id', referencedColumnName: 'id', nullable: false, onDelete: 'cascade')]
    private Hersteller $hersteller;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $refnummer;

    public function __construct(Artikel $artikel, Hersteller $hersteller, string $refnummer)
    {
        $this->artikel = $artikel;
        $this->hersteller = $hersteller;
        $this->refnummer = $refnummer;
    }

    public function getArtikel(): Artikel
    {
        return $this->artikel;
    }

    public function setArtikel(?Artikel $artikel): ArtikelToHerstRefnummer
    {
        $this->artikel = $artikel;
        return $this;
    }

    public function getHersteller(): Hersteller
    {
        return $this->hersteller;
    }

    public function setHersteller(Hersteller $hersteller): ArtikelToHerstRefnummer
    {
        $this->hersteller = $hersteller;
        return $this;
    }

    public function getRefnummer(): string
    {
        return $this->refnummer;
    }

    public function setRefnummer(string $refnummer): ArtikelToHerstRefnummer
    {
        $this->refnummer = $refnummer;
        return $this;
    }
}
