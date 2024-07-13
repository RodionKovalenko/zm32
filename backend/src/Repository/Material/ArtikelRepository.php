<?php

namespace App\Repository\Material;

use App\Entity\Department;
use App\Entity\DepartmentTyp;
use App\Entity\Material\Artikel;
use App\Repository\DefaultRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArtikelRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artikel::class);
    }

    public function getByDepartmentId(array $departments): array
    {
        $allDepartments = false;
        /** @var Department $department */
        foreach ($departments as $department) {
            if ($department->getTyp() === DepartmentTyp::ALLE->value) {
                $allDepartments = true;
                break;
            }
        }

        $q = $this->createQueryBuilder('a')
            ->leftJoin('a.departments', 'd');

        if (!$allDepartments) {
            $q->where('d IN (:departmentId)')
                ->setParameter('departmentId', $departments);
        }

        return $q->getQuery()
            ->getResult();
    }
}