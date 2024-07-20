<?php

namespace App\Repository\Material;

use App\Entity\Material\HerstellerToArtikel;
use App\Repository\DefaultRepository;
use Doctrine\Persistence\ManagerRegistry;

class HerstellerToArtikelRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HerstellerToArtikel::class);
    }
}