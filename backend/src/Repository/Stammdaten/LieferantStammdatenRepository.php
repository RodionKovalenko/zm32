<?php

namespace App\Repository\Stammdaten;

use App\Entity\Stammdaten\LieferantStammdaten;
use App\Repository\DefaultRepository;
use Doctrine\Persistence\ManagerRegistry;

class LieferantStammdatenRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LieferantStammdaten::class);
    }

}