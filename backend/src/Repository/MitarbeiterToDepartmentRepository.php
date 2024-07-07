<?php

namespace App\Repository;

use App\Entity\MitarbeiterToDepartment;
use Doctrine\Persistence\ManagerRegistry;

class MitarbeiterToDepartmentRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MitarbeiterToDepartment::class);
    }

}