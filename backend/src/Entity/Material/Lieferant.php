<?php

namespace App\Entity\Material;

use App\Entity\Bestellung;
use App\Entity\Stammdaten\LieferantStammdaten;
use App\Repository\Material\LieferantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use App\Validator\Constraints as AppAssert;

#[DoctrineAssert\UniqueEntity(fields: ['name'], errorPath: 'name', message: 'Lieferant mit dem gleichen Namen existiert bereits.')]
#[ORM\Entity(repositoryClass: LieferantRepository::class)]
class Lieferant
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[AppAssert\UniqueFieldValue(message: 'Lieferant mit dem gleichen Namen %s existiert bereits.', field: 'name', entity: Lieferant::class)]
    private string $name;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $typ = null;

    #[Groups(['Lieferant_LieferantStammdaten', 'LieferantStammdaten'])]
    #[ORM\OneToMany(
        mappedBy: 'lieferant',
        targetEntity: LieferantStammdaten::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $lieferantStammdaten;

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
        $this->lieferantStammdaten = new ArrayCollection();
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

    public function setName(string $name): void
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

    public function getBestellungen(): Collection
    {
        return $this->bestellungen;
    }

    public function setBestellungen(Collection $bestellungen): Lieferant
    {
        $this->bestellungen = $bestellungen;
        return $this;
    }

    public function getLieferantArtikels(): Collection
    {
        return $this->lieferantArtikels;
    }

    public function setLieferantArtikels(Collection $lieferantArtikels): Lieferant
    {
        $this->lieferantArtikels = $lieferantArtikels;
        return $this;
    }

    public function getLieferantStammdaten(): Collection
    {
        return $this->lieferantStammdaten;
    }

    public function addLieferantStammdaten(LieferantStammdaten $lieferantStammdaten): self
    {
        if (!$this->lieferantStammdaten->contains($lieferantStammdaten)) {
            $this->lieferantStammdaten[] = $lieferantStammdaten;
            $lieferantStammdaten->setLieferant($this); // Set the reverse reference
        }
        return $this;
    }

    public function removeLieferantStammdaten(LieferantStammdaten $lieferantStammdaten): self
    {
        $this->lieferantStammdaten->removeElement($lieferantStammdaten);
        return $this;
    }
}