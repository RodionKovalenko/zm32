<?php

namespace App\Entity;
use App\Entity\Material\Artikel;
use App\Entity\Material\Lieferant;
use App\Repository\BestellungRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use JMS\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: BestellungRepository::class)]
class Bestellung
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: false)]
    private ?\DateTimeInterface $datum = null;

    #[ORM\Column(type: Types::SMALLINT, length: 1, nullable: false)]
    private int $status;

    #[ORM\Column(type: Types::STRING, length: 512, nullable: true)]
    private string $amount;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $descriptionZusatz = null;

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

    #[Groups(['Artikel_Department', 'Department'])]
    #[ORM\ManyToMany(targetEntity: Department::class, inversedBy: "bestellungen")]
    #[ORM\JoinTable(name:"bestellung_to_departments")]
    #[ORM\JoinColumn(name: 'bestellung_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'department_id', referencedColumnName: 'id', nullable: false)]
    private Collection $departments;

    #[Groups(['Bestellung_Lieferant', 'Lieferant'])]
    #[ORM\ManyToOne(targetEntity: Lieferant::class, inversedBy: 'bestellungen')]
    #[ORM\JoinColumn(name: 'lieferant_id', referencedColumnName: 'id', nullable: true)]
    private Lieferant $lieferant;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Bestellung
    {
        $this->id = $id;
        return $this;
    }

    public function getDatum(): ?\DateTimeInterface
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

    public function getLieferant(): Lieferant
    {
        return $this->lieferant;
    }

    public function setLieferant(Lieferant $lieferant): Bestellung
    {
        $this->lieferant = $lieferant;
        return $this;
    }

    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function addDepartment(Department $department): self
    {
        if (!$this->departments->contains($department)) {
            $this->departments[] = $department;
            $department->addBestellung($this);
        }

        return $this;
    }

    public function removeDepartment(Department $department): self
    {
        if ($this->departments->removeElement($department)) {
            $department->removeBestellung($this);
        }

        return $this;
    }

    public function getPreis(): ?string
    {
        return $this->preis;
    }

    public function setPreis(?string $preis): Bestellung
    {
        $this->preis = $preis;
        return $this;
    }

    public function getDescriptionZusatz(): ?string
    {
        return $this->descriptionZusatz;
    }

    public function setDescriptionZusatz(?string $descriptionZusatz): Bestellung
    {
        $this->descriptionZusatz = $descriptionZusatz;
        return $this;
    }
}