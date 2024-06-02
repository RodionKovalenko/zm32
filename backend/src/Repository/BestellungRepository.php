<?php

namespace App\Repository;

use App\Entity\Bestellung;
use App\Entity\Mitarbeiter;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class BestellungRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bestellung::class);
    }

}