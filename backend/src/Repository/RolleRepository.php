<?php

namespace App\Repository;

use App\Entity\Rolle;
use Doctrine\Persistence\ManagerRegistry;

class RolleRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rolle::class);
    }

}

