<?php

namespace App\Repository\Material;

use App\Entity\Material\Lieferant;
use App\Repository\DefaultRepository;
use Doctrine\Common\Collections\Order;
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
            ->join('l.artikels', 'la')
            ->where('la.artikel = :artikelId')
            ->setParameter('artikelId', $artikelId)
            ->orderBy('l.name', Order::Ascending->value);

        return $q->getQuery()->getResult();
    }
}