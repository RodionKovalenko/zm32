<?php

namespace App\Entity\Material;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'artikel_to_lief_bestellnummer')]
class ArtikelToLieferBestellnummer
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Artikel::class, inversedBy: 'artikelToLieferantBestellnummers')]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false)]
    private ?Artikel $artikel = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Lieferant::class)]
    #[ORM\JoinColumn(name: 'lieferant_id', referencedColumnName: 'id', nullable: false)]
    private ?Lieferant $lieferant = null;

    #[ORM\Column(type: 'string', nullable: false)]
    private string $bestellnummer;

    public function getArtikel(): ?Artikel
    {
        return $this->artikel;
    }

    public function setArtikel(?Artikel $artikel): ArtikelToLieferBestellnummer
    {
        $this->artikel = $artikel;
        return $this;
    }

    public function getLieferant(): ?Lieferant
    {
        return $this->lieferant;
    }

    public function setLieferant(?Lieferant $lieferant): ArtikelToLieferBestellnummer
    {
        $this->lieferant = $lieferant;
        return $this;
    }

    public function getBestellnummer(): string
    {
        return $this->bestellnummer;
    }

    public function setBestellnummer(string $bestellnummer): ArtikelToLieferBestellnummer
    {
        $this->bestellnummer = $bestellnummer;
        return $this;
    }
}
