<?php

namespace App\Entity\Material;

use App\Entity\Bestellung;
use App\Repository\Material\HerstellerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use JMS\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use App\Validator\Constraints as AppAssert;

#[DoctrineAssert\UniqueEntity(fields: ['name'], errorPath: 'name', message: 'Hersteller mit dem gleichen Namen existiert bereits.')]
#[ORM\Entity(repositoryClass: HerstellerRepository::class)]
class Hersteller
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, unique: true)]
    #[AppAssert\UniqueFieldValue(message: 'Hersteller mit dem gleichen Namen %s existiert bereits.', field: 'name', entity: Hersteller::class)]
    private string $name;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $typ = null;

    #[Groups(['Hersteller_HerstellerStandort', 'HerstellerStandort'])]
    #[ORM\OneToMany(
        mappedBy: 'hersteller',
        targetEntity: HerstellerStandort::class,
        cascade: ['merge', 'persist', 'remove'],
        orphanRemoval: true
    )]
    private Collection $standorte;

    #[Groups(['Hersteller_Bestellung', 'Bestellung'])]
    #[ORM\ManyToMany(
        targetEntity: Bestellung::class,
        mappedBy: 'herstellers'
    )]
    private Collection $bestellungen;

    #[Groups(['Hersteller_Artikel', 'Artikel'])]
    #[ORM\ManyToMany(
        targetEntity: Artikel::class,
        mappedBy: 'herstellers'
    )]
    private Collection $artikels;


    public function __construct()
    {
        $this->standorte = new ArrayCollection();
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Hersteller
    {
        $this->name = $name;
        return $this;
    }

    public function getTyp(): ?string
    {
        return $this->typ;
    }

    public function setTyp(?string $typ): Hersteller
    {
        $this->typ = $typ;
        return $this;
    }

    public function getStandorte(): Collection
    {
        return $this->standorte;
    }

    public function setStandorte($standorte): self
    {
        // Ensure $standorte is a Collection
        if (!($standorte instanceof Collection)) {
            $standorte = new ArrayCollection($standorte);
        }

        // Remove old standorte
        foreach ($this->standorte as $standort) {
            if (!$standorte->contains($standort)) {
                $this->removeStandort($standort);
            }
        }

        // Add new standorte
        foreach ($standorte as $standort) {
            if (!$this->standorte->contains($standort)) {
                $this->addStandort($standort);
            }
        }

        return $this;
    }

    public function addStandort(HerstellerStandort $herstellerStandort): void
    {
        if (!$this->standorte->contains($herstellerStandort)) {
            $this->standorte->add($herstellerStandort);
            $herstellerStandort->setHersteller($this);
        }
    }

    public function removeStandort(HerstellerStandort $herstellerStandort): void
    {
        if ($this->standorte->contains($herstellerStandort)) {
            $this->standorte->removeElement($herstellerStandort);
            // Ensure that the Standorte's relationship to this Hersteller is also cleared
            $herstellerStandort->setHersteller(null);
        }
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
            $this->artikels->add($artikel);
            $artikel->addHersteller($this);
        }
        return $this;
    }

    public function removeArtikel(Artikel $artikel): self
    {
        if ($this->artikels->contains($artikel)) {
            $this->artikels->removeElement($artikel);
            $artikel->removeHersteller($this);
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
            $bestellung->addHersteller($this);
        }
        return $this;
    }

    public function removeBestellung(Bestellung $bestellung): self
    {
        if ($this->bestellungen->contains($bestellung)) {
            $this->bestellungen->removeElement($bestellung);
            $bestellung->removeHersteller($this);
        }
        return $this;
    }

}