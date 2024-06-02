<?php

namespace App\Entity\Material;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use JMS\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: Artikel::class)]
class Artikel
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[Groups(['Artikel_LieferantToArtikel', 'LieferantToArtikel'])]
    #[ORM\OneToMany(
        mappedBy: 'lieferant',
        targetEntity: LieferantToArtikel::class,
        cascade: ['persist', 'merge', 'remove']
    )]
    private Collection $lieferantToArtikels;

    public function __construct()
    {
        $this->lieferantToArtikels = new ArrayCollection();
    }

    public function getLieferantToArtikels(): Collection
    {
        return $this->lieferantToArtikels;
    }

    public function setLieferantToArtikels(Collection $lieferantToArtikels): void
    {
        $this->lieferantToArtikels = $lieferantToArtikels;
    }

    public function addLieferantToArtikel(LieferantToArtikel $lieferantToArtikel): self
    {
        if (!$this->lieferantToArtikels->contains($lieferantToArtikel)) {
            $this->lieferantToArtikels->add($lieferantToArtikel);
        }

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }
}