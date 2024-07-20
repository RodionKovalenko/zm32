<?php

namespace App\Entity\Material;

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


    public function __construct()
    {
        $this->standorte = new ArrayCollection();
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
}