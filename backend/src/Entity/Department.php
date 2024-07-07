<?php

namespace App\Entity;
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


    public function construct()
    {
        $this->bestellungen = new ArrayCollection();
        $this->mitarbeiterToDepartments = new ArrayCollection();
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
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     */
    public function getTyp()
    {
        return $this->typ;
    }

    /**
     * @param mixed $typ
     */
    public function setTyp($typ): void
    {
        $this->typ = $typ;
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

    public function getMitarbeiterToDepartments(): Collection
    {
        return $this->mitarbeiterToDepartments;
    }

    public function setMitarbeiterToDepartments(Collection $mitarbeiterToDepartments): Department
    {
        $this->mitarbeiterToDepartments = $mitarbeiterToDepartments;
        return $this;
    }
}