<?php

namespace App\Repository\Material;

use App\Entity\Material\LieferantToArtikel;
use App\Repository\DefaultRepository;
use Doctrine\Persistence\ManagerRegistry;

class LieferantToArtikelRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LieferantToArtikel::class);
    }
}