<?php

namespace App\Entity\Material;

use App\Repository\Material\HerstellerStandortRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: HerstellerStandortRepository::class)]
class HerstellerStandort
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[Groups(['HerstellerStandort_Hersteller', 'Hersteller'])]
    #[ORM\ManyToOne(targetEntity: Hersteller::class, inversedBy: 'herstellerStandorte')]
    #[ORM\JoinColumn(name: 'hersteller_id', referencedColumnName: 'id', nullable: false)]
    private ?Hersteller $hersteller = null;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    private ?string $adresse = null;
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true)]
    private ?string $ort = null;
    #[ORM\Column(type: Types::STRING, length: 10, nullable: true)]
    private ?string $plz = null;
    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    private ?string $url = null;

    #[ORM\Column(type: Types::STRING, length: 500, nullable: true)]
    private ?string $telefon = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): HerstellerStandort
    {
        $this->id = $id;
        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(?string $adresse): HerstellerStandort
    {
        $this->adresse = $adresse;
        return $this;
    }

    public function getOrt(): ?string
    {
        return $this->ort;
    }

    public function setOrt(?string $ort): HerstellerStandort
    {
        $this->ort = $ort;
        return $this;
    }

    public function getPlz(): ?string
    {
        return $this->plz;
    }

    public function setPlz(?string $plz): HerstellerStandort
    {
        $this->plz = $plz;
        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): HerstellerStandort
    {
        $this->url = $url;
        return $this;
    }

    public function getHersteller(): ?Hersteller
    {
        return $this->hersteller;
    }

    public function setHersteller(?Hersteller $hersteller = null): HerstellerStandort
    {
        $this->hersteller = $hersteller;
        return $this;
    }

    public function getTelefon(): ?string
    {
        return $this->telefon;
    }

    public function setTelefon(?string $telefon): HerstellerStandort
    {
        $this->telefon = $telefon;
        return $this;
    }
}