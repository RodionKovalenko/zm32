<?php

namespace App\Entity\Material;

use Doctrine\DBAL\Types\Types;
use JMS\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LieferantToArtikel::class)]
class LieferantToArtikel
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[Groups(['LieferantToArtikel_Artikel', 'Artikel'])]
    #[ORM\ManyToOne(targetEntity: Artikel::class, inversedBy: 'lieferantArtikels')]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false)]
    private Artikel $artikel;

    #[Groups(['LieferantToArtikel_Lieferant', 'Lieferant'])]
    #[ORM\ManyToOne(targetEntity: Lieferant::class)]
    #[ORM\JoinColumn(name: 'lieferant_id', referencedColumnName: 'id', nullable: false)]
    private Lieferant $lieferant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getArtikel(): Artikel
    {
        return $this->artikel;
    }

    public function setArtikel(Artikel $artikel): void
    {
        $this->artikel = $artikel;
    }

    public function getLieferant(): Lieferant
    {
        return $this->lieferant;
    }

    public function setLieferant(Lieferant $lieferant): void
    {
        $this->lieferant = $lieferant;
    }
}