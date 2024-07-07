<?php

namespace App\Entity\Material;

use App\Entity\Bestellung;
use App\Entity\Stammdaten\LieferantStammdaten;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: Lieferant::class)]
class Lieferant
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $typ = null;

    #[Groups(['Lieferant_LieferantStammdaten', 'LieferantStammdaten'])]
    #[ORM\OneToMany(
        mappedBy: 'lieferant',
        targetEntity: LieferantStammdaten::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $liferantStammdaten;

    #[Groups(['Lieferant_LieferantToArtikel', 'LieferantToArtikel'])]
    #[ORM\OneToMany(
        mappedBy: 'lieferant',
        targetEntity: LieferantToArtikel::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $lieferantArtikels;

    #[Groups(['Lieferant_Bestellung', 'Bestellung'])]
    #[ORM\OneToMany(
        mappedBy: 'lieferant',
        targetEntity: Bestellung::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $bestellungen;

    public function __construct()
    {
        $this->liferantStammdaten = new ArrayCollection();
        $this->bestellungen = new ArrayCollection();
        $this->lieferantArtikels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getTyp(): ?string
    {
        return $this->typ;
    }

    public function setTyp(?string $typ): void
    {
        $this->typ = $typ;
    }

    public function getLiferantStammdaten(): Collection
    {
        return $this->liferantStammdaten;
    }

    public function setLiferantStammdaten(Collection $liferantStammdaten): void
    {
        $this->liferantStammdaten = $liferantStammdaten;
    }

    public function getBestellungen(): Collection
    {
        return $this->bestellungen;
    }

    public function setBestellungen(Collection $bestellungen): Lieferant
    {
        $this->bestellungen = $bestellungen;
        return $this;
    }
}