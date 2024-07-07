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

}