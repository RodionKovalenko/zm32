<?php

namespace App\Repository\Material;

use App\Entity\Material\ArtikelToHerstRefnummer;
use App\Repository\DefaultRepository;
use Doctrine\Persistence\ManagerRegistry;

class ArtikelToHerstellerRefnummerRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ArtikelToHerstRefnummer::class);
    }

}