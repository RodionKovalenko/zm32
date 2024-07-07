<?php

namespace App\Entity;
use App\Entity\Material\Artikel;
use App\Entity\Material\Lieferant;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: Bestellung::class)]
class Bestellung
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    private \DateTimeInterface $datum;

    #[ORM\Column(type: Types::SMALLINT, length: 1, nullable: false)]
    private int $status;

    #[ORM\Column(type: Types::STRING, length: 512, nullable: true)]
    private string $amount;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $preis = null;

    #[Groups(['Bestellung_Artikel', 'Artikel'])]
    #[ORM\ManyToOne(targetEntity: Artikel::class, inversedBy: 'bestellungen')]
    #[ORM\JoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false)]
    private Artikel $artikel;

    #[Groups(['Bestellung_Mitarbeiter', 'Mitarbeiter'])]
    #[ORM\ManyToOne(targetEntity: Mitarbeiter::class, inversedBy: 'bestellungen')]
    #[ORM\JoinColumn(name: 'mitarbeiter_id', referencedColumnName: 'id', nullable: false)]
    private Mitarbeiter $mitarbeiter;

    #[Groups(['Bestellung_Department', 'Department'])]
    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'bestellungen')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id', nullable: false)]
    private Department $department;

    #[Groups(['Bestellung_Lieferant', 'Lieferant'])]
    #[ORM\ManyToOne(targetEntity: Lieferant::class, inversedBy: 'bestellungen')]
    #[ORM\JoinColumn(name: 'lieferant_id', referencedColumnName: 'id', nullable: true)]
    private Lieferant $lieferant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Bestellung
    {
        $this->id = $id;
        return $this;
    }

    public function getDatum(): \DateTimeInterface
    {
        return $this->datum;
    }

    public function setDatum(\DateTimeInterface $datum): Bestellung
    {
        $this->datum = $datum;
        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(int $status): Bestellung
    {
        $this->status = $status;
        return $this;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    public function setAmount(string $amount): Bestellung
    {
        $this->amount = $amount;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Bestellung
    {
        $this->description = $description;
        return $this;
    }

    public function getArtikel(): Artikel
    {
        return $this->artikel;
    }

    public function setArtikel(Artikel $artikel): Bestellung
    {
        $this->artikel = $artikel;
        return $this;
    }

    public function getMitarbeiter(): Mitarbeiter
    {
        return $this->mitarbeiter;
    }

    public function setMitarbeiter(Mitarbeiter $mitarbeiter): Bestellung
    {
        $this->mitarbeiter = $mitarbeiter;
        return $this;
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): Bestellung
    {
        $this->department = $department;
        return $this;
    }

    public function getLieferant(): Lieferant
    {
        return $this->lieferant;
    }

    public function setLieferant(Lieferant $lieferant): Bestellung
    {
        $this->lieferant = $lieferant;
        return $this;
    }
}