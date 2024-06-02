<?php

namespace App\Repository;

use App\Entity\MitarbeiterToDepartment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class MitarbeiterToDepartmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MitarbeiterToDepartment::class);
    }

}