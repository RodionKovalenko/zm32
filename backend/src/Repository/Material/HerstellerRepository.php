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

    public function getHerstellersByParams(array $params): array
    {
        $qb = $this->createQueryBuilder('h')
            ->leftJoin('h.standorte', 'hs');

        if (isset($params['search'])) {
            $qb->andWhere('(h.name LIKE :search) OR (hs.ort LIKE :search) OR (hs.adresse LIKE :search) OR (hs.plz LIKE :search)')
                ->setParameter('search', '%' . $params['search'] . '%');
        }

        return $qb->getQuery()->getResult();
    }
}