<?php

namespace App\Repository;

use App\Entity\Bestellung;
use App\Entity\DepartmentTyp;
use Doctrine\Persistence\ManagerRegistry;

class BestellungRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bestellung::class);
    }

    public function getByDepartment(array $deparments)
    {
        $isAllDepartment = false;

        foreach ($deparments as $department) {
            if ($department->getTyp() === DepartmentTyp::ALLE->value) {
                $isAllDepartment = true;
                break;
            }
        }

        $q = $this->createQueryBuilder('b')
            ->leftJoin('b.departments', 'd');

        if (!$isAllDepartment) {
            $q->where('d.id IN (:departmentId)')
                ->setParameter('departmentId', $deparments);
        }

        return $q->getQuery()
            ->getResult();
    }
}