<?php

namespace App\Repository\Material;

use App\Entity\Material\LieferantToArtikel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LieferantToArtikelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LieferantToArtikel::class);
    }
}