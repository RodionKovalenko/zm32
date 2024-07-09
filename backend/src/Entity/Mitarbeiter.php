<?php

namespace App\Entity;

use App\Entity\Material\Artikel;
use App\Repository\MitarbeiterRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MitarbeiterRepository::class)]
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

    #[Groups(['Mitarbeiter_User', 'User'])]
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private User $user;

    #[Groups(['Mitarbeiter_MitarbeiterToDepartment', 'MitarbeiterToDepartment'])]
    #[ORM\OneToMany(
        mappedBy: 'mitarbeiter',
        targetEntity: MitarbeiterToDepartment::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $mitarbeiterToDepartments;

    #[Groups(['Mitarbeiter_Bestellung', 'Bestellung'])]
    #[ORM\OneToMany(
        mappedBy: 'mitarbeiter',
        targetEntity: Bestellung::class,
        cascade: ['merge', 'persist', 'remove']
    )]
    private Collection $bestellungen;

    public function __construct()
    {
        $this->mitarbeiterToDepartments = new ArrayCollection();
        $this->bestellungen = new ArrayCollection();
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

    public function getMitarbeiterToDepartments(): Collection
    {
        return $this->mitarbeiterToDepartments;
    }

    public function setMitarbeiterToDepartments(Collection $mitarbeiterToDepartments): Mitarbeiter
    {
        $this->mitarbeiterToDepartments = $mitarbeiterToDepartments;
        return $this;
    }

    public function getBestellungen(): Collection
    {
        return $this->bestellungen;
    }

    public function setBestellungen(Collection $bestellungen): Mitarbeiter
    {
        $this->bestellungen = $bestellungen;
        return $this;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): Mitarbeiter
    {
        $this->user = $user;
        return $this;
    }
}
