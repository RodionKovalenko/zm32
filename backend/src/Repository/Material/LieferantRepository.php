<?php

namespace App\Repository\Material;

use App\Entity\Material\Lieferant;
use App\Repository\DefaultRepository;
use Doctrine\Persistence\ManagerRegistry;

class LieferantRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lieferant::class);
    }

    public function getByArtikel(int $artikelId)
    {
        $q = $this->createQueryBuilder('l')
            ->join('l.lieferantArtikels', 'la')
            ->where('la.artikel = :artikelId')
            ->setParameter('artikelId', $artikelId);

        return $q->getQuery()->getResult();
    }
}