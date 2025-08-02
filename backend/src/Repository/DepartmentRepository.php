<?php

namespace App\Repository;

use App\Entity\Department;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

class DepartmentRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

    public function getDeparmentByParams(array $params)
    {
        $search = $params['search'] ?? null;
        $departmentIds = $params['departmentIds'] ?? null;

        $q = $this->createQueryBuilder('l')
            ->orderBy('l.name', Order::Ascending->value);

        if (!empty($departmentIds)) {
            $q->andWhere('l IN (:departmentIds)')
                ->setParameter('departmentIds', $departmentIds);
        }

        if ($search) {
            $q->andWhere('l.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        return $q->getQuery()->getResult();
    }
}