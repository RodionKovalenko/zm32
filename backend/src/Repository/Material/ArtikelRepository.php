<?php

namespace App\Repository\Material;

use App\Entity\Material\Artikel;
use App\Repository\DefaultRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArtikelRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artikel::class);
    }
}