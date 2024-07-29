<?php

namespace App\Entity\Material;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'artikel_to_herst_refnummer')]
class ArtikelToHerstRefnummer
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Artikel::class, inversedBy: 'artikelToHerstRefnummers')]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false)]
    private ?Artikel $artikel = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Hersteller::class)]
    #[ORM\JoinColumn(name: 'hersteller_id', referencedColumnName: 'id', nullable: false)]
    private ?Hersteller $hersteller = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $refnummer;

    public function getArtikel(): ?Artikel
    {
        return $this->artikel;
    }

    public function setArtikel(?Artikel $artikel): ArtikelToHerstRefnummer
    {
        $this->artikel = $artikel;
        return $this;
    }

    public function getHersteller(): ?Hersteller
    {
        return $this->hersteller;
    }

    public function setHersteller(?Hersteller $hersteller): ArtikelToHerstRefnummer
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
