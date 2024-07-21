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
class Artikel
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, unique: true, nullable: false)]
    #[AppAssert\UniqueFieldValue(message: 'Artikel mit dem gleichen Namen %s existiert bereits.', field: 'name', entity: Artikel::class)]
    private string $name;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $model;

    #[Groups(['Artikel_Department', 'Department'])]
    #[ORM\ManyToMany(targetEntity: Department::class, inversedBy: 'artikels')]
    #[ORM\JoinTable(name: 'artikel_to_departments')]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'department_id', referencedColumnName: 'id', nullable: false)]
    private Collection $departments;

    #[Groups(['Artikel_Lieferant', 'Lieferant'])]
    #[ORM\ManyToMany(targetEntity: Lieferant::class, inversedBy: 'artikels')]
    #[ORM\JoinTable(name: 'artikel_to_lieferants')]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'lieferant_id', referencedColumnName: 'id', nullable: false)]
    private Collection $lieferants;

    #[Groups(['Artikel_Hersteller', 'Hersteller'])]
    #[ORM\ManyToMany(targetEntity: Hersteller::class, inversedBy: 'artikels')]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'hersteller_id', referencedColumnName: 'id', nullable: false)]
    private Collection $herstellers;

    #[Groups(['Artikel_Bestellung', 'Bestellung'])]
    #[ORM\OneToMany(
        mappedBy: 'artikel',
        targetEntity: Bestellung::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $bestellungen;

    public function __construct()
    {
        $this->lieferants = new ArrayCollection();
        $this->bestellungen = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->herstellers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    /**
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Artikel
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getBestellungen(): Collection
    {
        return $this->bestellungen;
    }

    public function setBestellungen(Collection $bestellungen): Artikel
    {
        $this->bestellungen = $bestellungen;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Artikel
    {
        $this->description = $description;
        return $this;
    }

    public function getModel(): ?string
    {
        return $this->model;
    }

    public function setModel(?string $model): Artikel
    {
        $this->model = $model;
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

    public function setHerstellers($herstellers)
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
            // Ensure that the Standorte's relationship to this Hersteller is also cleared
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
}