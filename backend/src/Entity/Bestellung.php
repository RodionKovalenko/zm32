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
    #[ORM\JoinColumn(name: 'lieferant_id', referencedColumnName: 'id', nullable: false)]
    private Lieferant $lieferant;
}