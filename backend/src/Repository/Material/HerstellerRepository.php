<?php

namespace App\Repository\Material;

use App\Entity\Material\Hersteller;
use App\Repository\DefaultRepository;
use Doctrine\Persistence\ManagerRegistry;

class HerstellerRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hersteller::class);
    }

    public function getByArtikelId($artikelId)
    {
        $qb = $this->createQueryBuilder('h')
            ->leftJoin('h.herstellerArtikels', 'ha')
            ->leftJoin('ha.artikel', 'a')
            ->where('a.id = :artikelId')
            ->setParameter('artikelId', $artikelId)
            ->getQuery()
            ->getResult();

        return $qb;
    }
}