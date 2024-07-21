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
    #[ORM\ManyToMany(targetEntity: Department::class, inversedBy: "artikels")]
    #[ORM\JoinTable(name:"artikel_to_departments")]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'department_id', referencedColumnName: 'id', nullable: false)]
    private Collection $departments;

    #[Groups(['Artikel_LieferantToArtikel', 'LieferantToArtikel'])]
    #[ORM\OneToMany(
        mappedBy: 'artikel',
        targetEntity: LieferantToArtikel::class,
        cascade: ['persist', 'merge', 'remove']
    )]
    private Collection $lieferantToArtikels;

    #[Groups(['Artikel_HerstellerToArtikel', 'HerstellerToArtikel'])]
    #[ORM\OneToMany(
        mappedBy: 'artikel',
        targetEntity: HerstellerToArtikel::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $herstellerArtikels;

    #[Groups(['Artikel_Bestellung', 'Bestellung'])]
    #[ORM\OneToMany(
        mappedBy: 'artikel',
        targetEntity: Bestellung::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $bestellungen;

    public function __construct()
    {
        $this->lieferantToArtikels = new ArrayCollection();
        $this->bestellungen = new ArrayCollection();
        $this->departments = new ArrayCollection();
        $this->herstellerArtikels = new ArrayCollection();
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

    public function addDepartment(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments[] = $department;
            $department->addArtikel($this);
        }

        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        if ($this->departments->removeElement($department)) {
            $department->removeArtikel($this);
        }

        return $this;
    }

    public function getHerstellerArtikels(): Collection
    {
        return $this->herstellerArtikels;
    }
    public function addHerstellerArtikels(HerstellerToArtikel $herstellerArtikels): void
    {
        if (!$this->herstellerArtikels->contains($herstellerArtikels)) {
            $this->herstellerArtikels[] = $herstellerArtikels;
        }
    }
    public function removeHerstellerArtikels(HerstellerToArtikel $herstellerArtikels): void
    {
        $this->herstellerArtikels->removeElement($herstellerArtikels);
    }
}