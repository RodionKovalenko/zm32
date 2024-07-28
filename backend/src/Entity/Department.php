<?php

namespace App\Entity;

use App\Entity\Material\Artikel;
use App\Repository\DepartmentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DepartmentRepository::class)]
class Department
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private $name;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    private $typ;

    #[Groups(['Department_MitarbeiterToDepartment', 'MitarbeiterToDepartment'])]
    #[ORM\OneToMany(
        mappedBy: 'department',
        targetEntity: MitarbeiterToDepartment::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $mitarbeiterToDepartments;

    #[Groups(['Department_Artikel', 'Artikel'])]
    #[ORM\ManyToMany(targetEntity: Artikel::class, mappedBy: "departments")]
    private Collection $artikels;

    #[Groups(['Department_Bestellung', 'Bestellung'])]
    #[ORM\ManyToMany(targetEntity: Bestellung::class, mappedBy: "departments")]
    private Collection $bestellungen;

    public function __construct()
    {
        $this->bestellungen = new ArrayCollection();
        $this->mitarbeiterToDepartments = new ArrayCollection();
        $this->artikels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Department
    {
        $this->id = $id;
        return $this;
    }

    /**
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return Department
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     */
    public function getTyp()
    {
        return $this->typ;
    }

    /**
     * @param mixed $typ
     * @return Department
     */
    public function setTyp($typ)
    {
        $this->typ = $typ;
        return $this;
    }

    public function getMitarbeiterToDepartments(): Collection
    {
        return $this->mitarbeiterToDepartments;
    }

    public function setMitarbeiterToDepartments(Collection $mitarbeiterToDepartments): Department
    {
        $this->mitarbeiterToDepartments = $mitarbeiterToDepartments;
        return $this;
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
            $artikel->addDepartment($this);
        }

        return $this;
    }

    public function removeArtikel(Artikel $artikel): self
    {
        if ($this->artikels->removeElement($artikel)) {
            $artikel->removeDepartment($this);
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
            $bestellung->addDepartment($this);
        }

        return $this;
    }

    public function removeBestellung(Bestellung $bestellung): self
    {
        if ($this->bestellungen->removeElement($bestellung)) {
            $bestellung->removeDepartment($this);
        }

        return $this;
    }
}