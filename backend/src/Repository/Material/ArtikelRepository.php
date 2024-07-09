<?php

namespace App\Repository\Material;

use App\Entity\Material\Artikel;
use App\Repository\DefaultRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArtikelRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artikel::class);
    }

    public function getByDepartmentId($departmentId): array
    {
        if (!is_array($departmentId)) {
            $departmentId = [$departmentId];
        }

        return $this->createQueryBuilder('a')
            ->leftJoin('a.departments', 'd')
            ->where('d IN (:departmentId)')
            ->setParameter('departmentId', $departmentId)
            ->getQuery()
            ->getResult();
    }
}