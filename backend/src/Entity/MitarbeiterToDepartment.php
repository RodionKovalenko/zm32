<?php

namespace App\Entity;

use App\Repository\MitarbeiterToDepartmentRepository;
use Doctrine\DBAL\Types\Types;
use JMS\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MitarbeiterToDepartmentRepository::class)]
class MitarbeiterToDepartment
{
    #[ORM\Id]
    #[ORM\Column(type: Types::INTEGER)]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[Groups(['MitarbeiterToDepartment_Mitarbeiter', 'Mitarbeiter'])]
    #[ORM\ManyToOne(targetEntity: Mitarbeiter::class, inversedBy: 'mitarbeiterToDepartments')]
    #[ORM\JoinColumn(name: 'mitarbeiter_id', referencedColumnName: 'id', nullable: false)]
    private Mitarbeiter $mitarbeiter;

    #[Groups(['MitarbeiterToDepartment_Department', 'Department'])]
    #[ORM\ManyToOne(targetEntity: Department::class, inversedBy: 'mitarbeiterToDepartments')]
    #[ORM\JoinColumn(name: 'department_id', referencedColumnName: 'id', nullable: false)]
    private Department $department;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getMitarbeiter(): Mitarbeiter
    {
        return $this->mitarbeiter;
    }

    public function setMitarbeiter(Mitarbeiter $mitarbeiter): void
    {
        $this->mitarbeiter = $mitarbeiter;
    }

    public function getDepartment(): Department
    {
        return $this->department;
    }

    public function setDepartment(Department $department): void
    {
        $this->department = $department;
    }
}