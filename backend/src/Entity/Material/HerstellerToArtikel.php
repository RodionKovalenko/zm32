<?php

namespace App\Entity\Material;

use App\Repository\Material\HerstellerToArtikelRepository;
use Doctrine\DBAL\Types\Types;
use JMS\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HerstellerToArtikelRepository::class)]
class HerstellerToArtikel
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[Groups(['HerstellerToArtikel_Artikel', 'Artikel'])]
    #[ORM\ManyToOne(targetEntity: Artikel::class, inversedBy: 'herstellerArtikels')]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false)]
    private Artikel $artikel;

    #[Groups(['HerstellerToArtikel_Hersteller', 'Hersteller'])]
    #[ORM\ManyToOne(targetEntity: Lieferant::class)]
    #[ORM\JoinColumn(name: 'hersteller_id', referencedColumnName: 'id', nullable: false)]
    private Hersteller $hersteller;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): HerstellerToArtikel
    {
        $this->id = $id;
        return $this;
    }

    public function getArtikel(): Artikel
    {
        return $this->artikel;
    }

    public function setArtikel(Artikel $artikel): HerstellerToArtikel
    {
        $this->artikel = $artikel;
        return $this;
    }

    public function getHersteller(): Hersteller
    {
        return $this->hersteller;
    }

    public function setHersteller(Hersteller $hersteller): HerstellerToArtikel
    {
        $this->hersteller = $hersteller;
        return $this;
    }
}