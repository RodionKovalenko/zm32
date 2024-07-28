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
        cascade: ['merge', 'persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $lieferantStammdaten;

    #[Groups(['Lieferant_Artikel', 'Artikel'])]
    #[ORM\ManyToMany(
        targetEntity: Artikel::class,
        mappedBy: 'lieferants'
    )]
    private Collection $artikels;

    #[Groups(['Lieferant_Bestellung', 'Bestellung'])]
    #[ORM\ManyToMany(
        targetEntity: Lieferant::class,
        mappedBy: 'lieferants'
    )]
    private Collection $bestellungen;

    public function __construct()
    {
        $this->lieferantStammdaten = new ArrayCollection();
        $this->bestellungen = new ArrayCollection();
        $this->artikels = new ArrayCollection();
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

    public function getArtikels(): Collection
    {
        return $this->artikels;
    }

    public function setArtikels($artikels): self
    {
        if (!($artikels instanceof Collection)) {
            $artikels = new ArrayCollection($artikels);
        }
        $this->artikels = $artikels;
        return $this;
    }

    public function addArtikel(Artikel $artikel): self
    {
        if (!$this->artikels->contains($artikel)) {
            $this->artikels[] = $artikel;
            $artikel->addLieferant($this);
        }
        return $this;
    }

    public function removeArtikel(Artikel $artikel): self
    {
        if ($this->artikels->contains($artikel)) {
            $this->artikels->removeElement($artikel);
            $artikel->removeLieferant($this);
        }
        return $this;
    }

    public function getBestellungen(): Collection
    {
        return $this->bestellungen;
    }

    public function setBestellungen($bestellungen): self
    {
        if (!($bestellungen instanceof Collection)) {
            $bestellungen = new ArrayCollection($bestellungen);
        }
        $this->bestellungen = $bestellungen;
        return $this;
    }

    public function addBestellung(Bestellung $bestellung): self
    {
        if (!$this->bestellungen->contains($bestellung)) {
            $this->bestellungen[] = $bestellung;
            $bestellung->addLieferant($this);
        }
        return $this;
    }

    public function removeBestellung(Bestellung $bestellung): self
    {
        if ($this->bestellungen->contains($bestellung)) {
            $this->bestellungen->removeElement($bestellung);
            $bestellung->removeLieferant($this);
        }
        return $this;
    }

    public function setLieferantStammdaten($lieferantStammdaten)
    {
        // Ensure $lieferantStammdaten is a Collection
        if (!($lieferantStammdaten instanceof Collection)) {
            $lieferantStammdaten = new ArrayCollection($lieferantStammdaten);
        }

        // Remove old lieferantStammdaten
        foreach ($this->lieferantStammdaten as $lieferantStamm) {
            if (!$lieferantStammdaten->contains($lieferantStamm)) {
                $this->removeLieferantStammdaten($lieferantStamm);
            }
        }

        // Add new lieferantStammdaten
        foreach ($lieferantStammdaten as $lieferantStamm) {
            if (!$this->lieferantStammdaten->contains($lieferantStamm)) {
                $this->addLieferantStammdaten($lieferantStamm);
            }
        }

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
        if ($this->lieferantStammdaten->contains($lieferantStammdaten)) {
            $this->lieferantStammdaten->removeElement($lieferantStammdaten);
            // Ensure that the Standorte's relationship to this Hersteller is also cleared
            $lieferantStammdaten->setLieferant(null);
        }

        return $this;
    }
}