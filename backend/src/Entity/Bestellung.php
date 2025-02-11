<?php

namespace App\Entity;
use App\Entity\Material\Artikel;
use App\Entity\Material\Hersteller;
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
    private ?string $amount = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $descriptionZusatz = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $preis = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $gesamtpreis = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    private ?string $packageunit = null;

    #[Groups(['Bestellung_Department', 'Department'])]
    #[ORM\ManyToMany(targetEntity: Department::class, inversedBy: "bestellungen")]
    #[ORM\JoinTable(name:"bestellung_to_departments")]
    #[ORM\JoinColumn(name: 'bestellung_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'department_id', referencedColumnName: 'id', nullable: false)]
    private Collection $departments;

    #[Groups(['Bestellung_Artikel', 'Artikel'])]
    #[ORM\ManyToMany(targetEntity: Artikel::class)]
    #[ORM\JoinTable(name:"bestellung_to_artikels")]
    #[ORM\JoinColumn(name: 'bestellung_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'artikel_id', referencedColumnName: 'id', nullable: false)]
    private Collection $artikels;

    #[Groups(['Bestellung_Mitarbeiter', 'Mitarbeiter'])]
    #[ORM\ManyToOne(targetEntity: Mitarbeiter::class, inversedBy: 'bestellungen')]
    #[ORM\JoinColumn(name: 'mitarbeiter_id', referencedColumnName: 'id', nullable: false)]
    private Mitarbeiter $mitarbeiter;

    #[Groups(['Bestellung_Lieferant', 'Lieferant'])]
    #[ORM\ManyToMany(targetEntity: Lieferant::class, inversedBy: "bestellungen")]
    #[ORM\JoinTable(name:"bestellung_to_lieferants")]
    #[ORM\JoinColumn(name: 'bestellung_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'lieferant_id', referencedColumnName: 'id', nullable: false)]
    private Collection $lieferants;

    #[Groups(['Bestellung_Hersteller', 'Hersteller'])]
    #[ORM\ManyToMany(targetEntity: Hersteller::class, inversedBy: "bestellungen")]
    #[ORM\JoinTable(name:"bestellung_to_herstellers")]
    #[ORM\JoinColumn(name: 'bestellung_id', referencedColumnName: 'id', nullable: false)]
    #[ORM\InverseJoinColumn(name: 'hersteller_id', referencedColumnName: 'id', nullable: false)]
    private Collection $herstellers;

    public function __construct()
    {
        $this->departments = new ArrayCollection();
        $this->lieferants = new ArrayCollection();
        $this->herstellers = new ArrayCollection();
        $this->artikels = new ArrayCollection();
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

    public function getAmount(): ?string
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

    public function getMitarbeiter(): Mitarbeiter
    {
        return $this->mitarbeiter;
    }

    public function setMitarbeiter(Mitarbeiter $mitarbeiter): Bestellung
    {
        $this->mitarbeiter = $mitarbeiter;
        return $this;
    }

    public function getDepartments(): Collection
    {
        return $this->departments;
    }

    public function setDepartments($departments): self
    {
        if (!($departments instanceof Collection)) {
            $departments = new ArrayCollection($departments);
        }
        $this->departments = $departments;
        return $this;
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

    public function getLieferants(): Collection
    {
        return $this->lieferants;
    }

    public function setLieferants($lieferants): self
    {
        if (!($lieferants instanceof Collection)) {
            $lieferants = new ArrayCollection($lieferants);
        }
        $this->lieferants = $lieferants;
        return $this;
    }

    public function addLieferant(Lieferant $lieferant): self
    {
        if (!$this->lieferants->contains($lieferant)) {
            $this->lieferants[] = $lieferant;
            $lieferant->addBestellung($this);
        }

        return $this;
    }

    public function removeLieferant(Lieferant $lieferant): self
    {
        if ($this->lieferants->removeElement($lieferant)) {
            $lieferant->removeBestellung($this);
        }

        return $this;
    }

    public function getHerstellers(): Collection
    {
        return $this->herstellers;
    }

    public function setHerstellers($herstellers): self
    {
        if (!($herstellers instanceof Collection)) {
            $herstellers = new ArrayCollection($herstellers);
        }
        $this->herstellers = $herstellers;
        return $this;
    }

    public function addHersteller(Hersteller $hesteller): self
    {
        if (!$this->herstellers->contains($hesteller)) {
            $this->herstellers[] = $hesteller;
            $hesteller->addBestellung($this);
        }

        return $this;
    }

    public function removeHersteller(Hersteller $hersteller): self
    {
        if ($this->lieferants->removeElement($hersteller)) {
            $hersteller->removeBestellung($this);
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
        }

        return $this;
    }

    public function removeArtikel(Artikel $artikel): self
    {
        if ($this->artikels->removeElement($artikel)) {
            $artikel->removeBestellung($this);
        }

        return $this;
    }

    public function getGesamtpreis(): ?string
    {
        return $this->gesamtpreis;
    }

    public function setGesamtpreis(?string $gesamtpreis): Bestellung
    {
        $this->gesamtpreis = $gesamtpreis;
        return $this;
    }

    public function getPackageunit(): ?string
    {
        return $this->packageunit;
    }

    public function setPackageunit(?string $packageunit): Bestellung
    {
        $this->packageunit = $packageunit;
        return $this;
    }
}