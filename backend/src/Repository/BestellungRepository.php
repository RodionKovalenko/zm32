<?php

namespace App\Repository;

use App\Entity\Bestellung;
use Doctrine\Persistence\ManagerRegistry;

class BestellungRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bestellung::class);
    }

    public function getByDepartment($deparmentId)
    {
        if (!is_array($deparmentId)) {
            $deparmentId = [$deparmentId];
        }

        return $this->createQueryBuilder('b')
            ->leftJoin('b.departments', 'd')
            ->where('d.id IN (:departmentId)')
            ->setParameter('departmentId', $deparmentId)
            ->getQuery()
            ->getResult();
    }
}