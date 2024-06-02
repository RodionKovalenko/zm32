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

    #[Groups(['Department_Mitarbeiter', 'Mitarbeiter'])]
    #[ORM\OneToMany(
        mappedBy: 'mitarbeiter',
        targetEntity: Mitarbeiter::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $mitarbeiters;

    public function construct()
    {
        $this->mitarbeiters = new ArrayCollection();
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

    public function getMitarbeiters(): Collection
    {
        return $this->mitarbeiters;
    }

    public function setMitarbeiters(Collection $mitarbeiters): void
    {
        $this->mitarbeiters = $mitarbeiters;
    }
}