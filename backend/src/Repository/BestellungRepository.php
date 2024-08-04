<?php

namespace App\Repository;

use App\Entity\Bestellung;
use App\Entity\DepartmentTyp;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

class BestellungRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bestellung::class);
    }

    public function getByDepartment(array $filterParams = [])
    {
        $isAllDepartment = false;
        $deparments = $filterParams['departments'] ?? [];
        $status = $filterParams['status'] ?? [];
        $createdAfter = $filterParams['createdAfter'] ?? [];

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

        if (!empty($status)) {
            $q->andWhere('b.status IN (:status)')
                ->setParameter('status', $status);
        }
        if (!empty($createdAfter)) {
            $q->andWhere('b.datum >= :createdAfter')
                ->setParameter('createdAfter', $createdAfter);
        }

        return $q->orderBy('d.name', Order::Ascending->value)
            ->getQuery()
            ->getResult();
    }
}