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

    public function getLieferantsByParams(array $params)
    {
        $q = $this->createQueryBuilder('l');

        if (isset($params['search'])) {
            $q->andWhere('l.name LIKE :search')
                ->setParameter('search', '%' . $params['search'] . '%');
        }

        return $q->getQuery()->getResult();
    }
}