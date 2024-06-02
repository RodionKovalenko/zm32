<?php

namespace App\Entity;

use App\Entity\Material\Lieferant;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: Mitarbeiter::class)]
class Mitarbeiter
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private $vorname;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private $nachame;

    #[Groups(['Mitarbeiter_Lieferant', 'Lieferant'])]
    #[ORM\OneToMany(
        mappedBy: 'mitarbeiter',
        targetEntity: Lieferant::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $lieferante;

    #[Groups(['Mitarbeiter_Department', 'Department'])]
    #[ORM\OneToMany(
        mappedBy: 'department',
        targetEntity: Department::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $departments;

    public function construct()
    {
        $this->lieferante = new ArrayCollection();
        $this->departments = new ArrayCollection();
    }

    /**
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    public function getLieferante(): Collection
    {
        return $this->lieferante;
    }

    public function setLieferante(Collection $lieferante): void
    {
        $this->lieferante = $lieferante;
    }

    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function setDepartments(Collection $departments): void
    {
        $this->departments = $departments;
    }

    /**
     */
    public function getVorname()
    {
        return $this->vorname;
    }

    /**
     * @param mixed $vorname
     */
    public function setVorname($vorname): void
    {
        $this->vorname = $vorname;
    }

    /**
     */
    public function getNachame()
    {
        return $this->nachame;
    }

    /**
     * @param mixed $nachame
     */
    public function setNachame($nachame): void
    {
        $this->nachame = $nachame;
    }
}
