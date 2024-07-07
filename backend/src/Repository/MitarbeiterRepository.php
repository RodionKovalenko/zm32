<?php

namespace App\Repository;

use App\Entity\Mitarbeiter;
use Doctrine\Persistence\ManagerRegistry;

class MitarbeiterRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mitarbeiter::class);
    }

}