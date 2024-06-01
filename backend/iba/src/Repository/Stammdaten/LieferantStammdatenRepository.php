<?php

namespace App\Repository\Stammdaten;

use App\Entity\Stammdaten\LieferantStammdaten;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class LieferantStammdatenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LieferantStammdaten::class);
    }

}