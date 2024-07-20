<?php

namespace App\Repository\Material;

use App\Entity\Material\HerstellerStandort;
use App\Repository\DefaultRepository;
use Doctrine\Persistence\ManagerRegistry;

class HerstellerStandortRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HerstellerStandort::class);
    }
}