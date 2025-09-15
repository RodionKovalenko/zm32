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

        $q->orderBy('l.name', 'ASC');

        return $q->getQuery()->getResult();
    }

    public function findByNamesCaseInsenstitive(array $names): array
    {
        $q = $this->createQueryBuilder('l')
            ->where('LOWER(l.name) IN (:names)')
            ->setParameter('names', array_map('strtolower', $names));

        return $q->getQuery()->getResult();
    }
}