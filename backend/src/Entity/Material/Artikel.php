<?php

namespace App\Entity\Material;

use App\Entity\Bestellung;
use App\Entity\Department;
use App\Repository\Material\ArtikelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use JMS\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use App\Validator\Constraints as AppAssert;

#[DoctrineAssert\UniqueEntity(fields: ['name'], errorPath: 'name', message: 'Lieferant mit dem gleichen Namen existiert bereits.')]
#[ORM\Entity(repositoryClass: ArtikelRepository::class)]
#[ORM\Table(name: 'artikel')]
#[ORM\Index(columns: ['name'], name: 'name_idx')]
class Artikel
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, unique: true, nullable: false)]
    #[AppAssert\UniqueFieldValue(message: 'Artikel mit dem gleichen Namen %s existiert bereits.', field: 'name', entity: Artikel::class)]
    private string $name;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $model;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $preis = null;

    #[Groups(['Artikel_Department', 'Department'])]
    #[ORM\ManyToMany(targetEntity: Department::class, inversedBy: 'artikels')]
    #[ORM\JoinTable(name: 'artikel_to_departments')]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false, onDelete: 'restrict')]
    #[ORM\InverseJoinColumn(name: 'department_id', referencedColumnName: 'id', nullable: false, onDelete: 'cascade')]
    private Collection $departments;

    #[Groups(['Artikel_Lieferant', 'Lieferant'])]
    #[ORM\ManyToMany(targetEntity: Lieferant::class, inversedBy: 'artikels')]
    #[ORM\JoinTable(name: 'artikel_to_lieferants')]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false, onDelete: 'restrict')]
    #[ORM\InverseJoinColumn(name: 'lieferant_id', referencedColumnName: 'id', nullable: false, onDelete: 'cascade')]
    private Collection $lieferants;

    #[Groups(['Artikel_Hersteller', 'Hersteller'])]
    #[ORM\ManyToMany(targetEntity: Hersteller::class, inversedBy: 'artikels')]
    #[ORM\JoinTable(name: 'artikel_to_herstellers')]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false, onDelete: 'restrict')]
    #[ORM\InverseJoinColumn(name: 'hersteller_id', referencedColumnName: 'id', nullable: false, onDelete: 'cascade')]
    private Collection $herstellers;

    #[Groups(['Artikel_Bestellung', 'Bestellung'])]
    #[ORM\ManyToMany(targetEntity: Bestellung::class, mappedBy: 'artikels')]
    private Collection $bestellungen;

    #[Groups(['Artikel_ArtikelToLieferBestellnummer', 'ArtikelToLieferBestellnummer'])]
    #[ORM\OneToMany(mappedBy: 'artikel', targetEntity: ArtikelToLieferBestellnummer::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $artikelToLieferantBestellnummers;

    #[Groups(['Artikel_ArtikelToHerstRefnummer', 'ArtikelToHerstRefnummer'])]
    #[ORM\OneToMany(mappedBy: 'artikel', targetEntity: ArtikelToHerstRefnummer::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $artikelToHerstRefnummers;

    public function __construct()
    {
        $this->lieferants = new ArrayCollection();
        $this->bestellungen = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->herstellers = new ArrayCollection();
        $this->artikelToLieferantBestellnummers = new ArrayCollection();
        $this->artikelToHerstRefnummers = new ArrayCollection();
    }

    #[ORM\Table(name: 'artikel_to_departments', indexes: [new ORM\Index(name: 'artikel_department_idx', columns: ['artikel_id', 'department_id'])])]
    #[ORM\Table(name: 'artikel_to_lieferants', indexes: [new ORM\Index(name: 'artikel_lieferant_idx', columns: ['artikel_id', 'lieferant_id'])])]
    #[ORM\Table(name: 'artikel_to_herstellers', indexes: [new ORM\Index(name: 'artikel_hersteller_idx', columns: ['artikel_id', 'hersteller_id'])])]
    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function getPreis(): ?string
    {
        return $this->preis;
    }

    public function setPreis(?string $preis): Artikel
    {
        $this->preis = $preis;
        return $this;
    }

    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function setDepartments($departments): self
    {
        if (!($departments instanceof Collection)) {
            $departments = new ArrayCollection($departments);
        }
        $this->departments = $departments;
        return $this;
    }

    public function addDepartment(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments->add($department);
            $department->addArtikel($this);
        }
        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        if ($this->departments->contains($department) &&
            $this->departments->removeElement($department)) {
            $department->removeArtikel($this);
        }
        return $this;
    }

    public function getHerstellers(): Collection
    {
        return $this->herstellers;
    }

    public function setHerstellers($herstellers): self
    {
        if (!($herstellers instanceof Collection)) {
            $herstellers = new ArrayCollection($herstellers);
        }
        $this->herstellers = $herstellers;
        return $this;
    }

    public function addHersteller(Hersteller $hersteller): void
    {
        if (!$this->herstellers->contains($hersteller)) {
            $this->herstellers->add($hersteller);
            $hersteller->addArtikel($this);
        }
    }

    public function removeHersteller(Hersteller $hersteller): void
    {
        if ($this->herstellers->contains($hersteller)) {
            $this->herstellers->removeElement($hersteller);
            $hersteller->removeArtikel($this);
        }
    }

    public function getLieferants(): Collection
    {
        return $this->lieferants;
    }

    public function setLieferants($lieferants): self
    {
        if (!($lieferants instanceof Collection)) {
            $lieferants = new ArrayCollection($lieferants);
        }
        $this->lieferants = $lieferants;
        return $this;
    }

    public function addLieferant(Lieferant $lieferant): self
    {
        if (!$this->lieferants->contains($lieferant)) {
            $this->lieferants->add($lieferant);
            $lieferant->addArtikel($this);
        }
        return $this;
    }

    public function removeLieferant(Lieferant $lieferant): self
    {
        if ($this->lieferants->contains($lieferant) &&
            $this->lieferants->removeElement($lieferant)) {
            $lieferant->removeArtikel($this);
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
            $this->bestellungen->add($bestellung);
            $bestellung->addArtikel($this);
        }
        return $this;
    }

    public function removeBestellung(Bestellung $bestellung): self
    {
        if ($this->bestellungen->contains($bestellung) &&
            $this->bestellungen->removeElement($bestellung)) {
            $bestellung->removeArtikel($this);
        }
        return $this;
    }


    public function getArtikelToLieferantBestellnummers(): Collection
    {
        return $this->artikelToLieferantBestellnummers;
    }

    public function setArtikelToLieferantBestellnummers($artikelToLieferantBestellnummers): self
    {
        if (!($artikelToLieferantBestellnummers instanceof Collection)) {
            $artikelToLieferantBestellnummers = new ArrayCollection($artikelToLieferantBestellnummers);
        }
        $this->artikelToLieferantBestellnummers = $artikelToLieferantBestellnummers;
        return $this;
    }

    public function addArtikelToLieferantBestellnummer(ArtikelToLieferBestellnummer $artikelToLieferantBestellnummer): self
    {
        if (!$this->artikelToLieferantBestellnummers->contains($artikelToLieferantBestellnummer)) {
            $this->artikelToLieferantBestellnummers->add($artikelToLieferantBestellnummer);
            $artikelToLieferantBestellnummer->setArtikel($this);
        }
        return $this;
    }

    public function removeArtikelToLieferantBestellnummer(ArtikelToLieferBestellnummer $artikelToLieferantBestellnummer): self
    {
        if ($this->artikelToLieferantBestellnummers->contains($artikelToLieferantBestellnummer)) {
            $this->artikelToLieferantBestellnummers->removeElement($artikelToLieferantBestellnummer);
            // set the owning side to null (unless already changed)
            if ($artikelToLieferantBestellnummer->getArtikel() === $this) {
                $artikelToLieferantBestellnummer->setArtikel(null);
            }
        }
        return $this;
    }

    public function getArtikelToHerstRefnummers(): Collection
    {
        return $this->artikelToHerstRefnummers;
    }

    public function setArtikelToHerstRefnummers($artikelToHerstRefnummers): self
    {
        if (!($artikelToHerstRefnummers instanceof Collection)) {
            $artikelToHerstRefnummers = new ArrayCollection($artikelToHerstRefnummers);
        }
        $this->artikelToHerstRefnummers = $artikelToHerstRefnummers;
        return $this;
    }

    public function addArtikelToHerstRefnummer(ArtikelToHerstRefnummer $artikelToHerstRefnummer): self
    {
        if (!$this->artikelToHerstRefnummers->contains($artikelToHerstRefnummer)) {
            $this->artikelToHerstRefnummers->add($artikelToHerstRefnummer);
            $artikelToHerstRefnummer->setArtikel($this);
        }
        return $this;
    }

    public function removeArtikelToHerstRefnummer(ArtikelToHerstRefnummer $artikelToHerstRefnummer): self
    {
        if ($this->artikelToHerstRefnummers->contains($artikelToHerstRefnummer)) {
            $this->artikelToHerstRefnummers->removeElement($artikelToHerstRefnummer);
            // set the owning side to null (unless already changed)
            if ($artikelToHerstRefnummer->getArtikel() === $this) {
                $artikelToHerstRefnummer->setArtikel(null);
            }
        }
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): Artikel
    {
        $this->url = $url;
        return $this;
    }

}