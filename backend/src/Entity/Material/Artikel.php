<?php

namespace App\Entity\Material;

use App\Entity\Bestellung;
use App\Entity\Department;
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

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $name;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $description;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $model;

    #[Groups(['Artikel_Department', 'Department'])]
    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'artikels')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id', nullable: false)]
    private Department $department;

    #[Groups(['Artikel_LieferantToArtikel', 'LieferantToArtikel'])]
    #[ORM\OneToMany(
        mappedBy: 'artikel',
        targetEntity: LieferantToArtikel::class,
        cascade: ['persist', 'merge', 'remove']
    )]
    private Collection $lieferantToArtikels;

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

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): Artikel
    {
        $this->department = $department;
        return $this;
    }
}