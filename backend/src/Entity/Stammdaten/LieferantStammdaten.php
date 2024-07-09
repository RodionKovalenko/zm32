<?php

namespace App\Entity\Stammdaten;

use App\Entity\Material\Lieferant;
use App\Repository\Stammdaten\LieferantStammdatenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: LieferantStammdatenRepository::class)]
class LieferantStammdaten
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[Groups(['LieferantStammdaten_Lieferant', 'Lieferant'])]
    #[ORM\ManyToOne(
        targetEntity: Lieferant::class,
        cascade: ['persist', 'merge', 'remove']
    )]
    #[ORM\JoinColumn(name: 'oeffnungszeiten_id', referencedColumnName: 'id', nullable: false)]
    private Lieferant $lieferant;

    #[ORM\Column(type: Types::STRING, length: 150, nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $startdatum = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $enddatum = null;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    private ?string $ort = null;

    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    private ?string $plz = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getLieferant(): Lieferant
    {
        return $this->lieferant;
    }

    public function setLieferant(Lieferant $lieferant): void
    {
        $this->lieferant = $lieferant;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getStartdatum(): ?\DateTimeInterface
    {
        return $this->startdatum;
    }

    public function setStartdatum(?\DateTimeInterface $startdatum): void
    {
        $this->startdatum = $startdatum;
    }

    public function getEnddatum(): ?\DateTimeInterface
    {
        return $this->enddatum;
    }

    public function setEnddatum(?\DateTimeInterface $enddatum): void
    {
        $this->enddatum = $enddatum;
    }

    public function getOrt(): ?string
    {
        return $this->ort;
    }

    public function setOrt(?string $ort): void
    {
        $this->ort = $ort;
    }

    public function getPlz(): ?string
    {
        return $this->plz;
    }

    public function setPlz(?string $plz): void
    {
        $this->plz = $plz;
    }
}