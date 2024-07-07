<?php

namespace App\Entity;
use App\Entity\Material\Artikel;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: Department::class)]
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

    #[Groups(['Department_Bestellung', 'Bestellung'])]
    #[ORM\OneToMany(
        mappedBy: 'department',
        targetEntity: Bestellung::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $bestellungen;

    #[Groups(['Department_Artikel', 'Artikel'])]
    #[ORM\OneToMany(
        mappedBy: 'department',
        targetEntity: Artikel::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $artikels;


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

    public function getBestellungen(): Collection
    {
        return $this->bestellungen;
    }

    public function setBestellungen(Collection $bestellungen): Department
    {
        $this->bestellungen = $bestellungen;
        return $this;
    }

    public function getArtikels(): Collection
    {
        return $this->artikels;
    }

    public function setArtikels(Collection $artikels): Department
    {
        $this->artikels = $artikels;
        return $this;
    }
}