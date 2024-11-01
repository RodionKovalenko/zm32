<?php

namespace App\Repository\Material;

use App\Entity\Material\Artikel;
use App\Repository\DefaultRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

class ArtikelRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Artikel::class);
    }

    public function getByParams(array $params): array
    {
        $searchWord = $params['search'] ?? null;
        $departmentsId =  $params['departmentIds'] ?? null;

        $q = $this->createQueryBuilder('a')
            ->leftJoin('a.departments', 'd');

        if (!empty($departmentsId)) {
            $q->andWhere('d.id IN (:departmentsId)')
                ->setParameter('departmentsId', $departmentsId);
        }

        if (!empty($searchWord)) {
            $q->leftJoin('a.artikelToHerstRefnummers', 'hrn')
                ->leftJoin('a.artikelToLieferantBestellnummers', 'lbn')
                ->leftJoin('a.herstellers', 'h')
                ->leftJoin('a.lieferants', 'l')
                ->andWhere(
                    '((a.name LIKE :searchWord)
                 OR (h.name LIKE :searchWord)
                 OR (a.description LIKE :searchWord)
                 OR (l.name LIKE :searchWord)
                  OR (hrn.refnummer LIKE :searchWord)
                   OR (lbn.bestellnummer LIKE :searchWord))'
                )
                ->setParameter('searchWord', '%' . $searchWord . '%');
        }

        return $q->orderBy('a.name', Order::Ascending->value)
            ->setMaxResults(1000)
            ->getQuery()
            ->getResult();
    }
}