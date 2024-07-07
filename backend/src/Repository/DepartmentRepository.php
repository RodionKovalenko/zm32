<?php

namespace App\Repository;

use App\Entity\Department;
use Doctrine\Persistence\ManagerRegistry;

class DepartmentRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Department::class);
    }

}