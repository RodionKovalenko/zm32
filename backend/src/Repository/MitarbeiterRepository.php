<?php

namespace App\Repository;

use App\Entity\Mitarbeiter;
use Doctrine\Persistence\ManagerRegistry;

class MitarbeiterRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Mitarbeiter::class);
    }

    public function getMitarbeiterByUserMitarbeiterId(int $mitarbeiterId): ?Mitarbeiter
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.user', 'u')
            ->where('u.mitarbeiterId = :mitarbeiterId')
            ->setParameter('mitarbeiterId', $mitarbeiterId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getUserByParams(array $params = [])
    {
        $qb = $this->createQueryBuilder('m');

        if (isset($params['search'])) {
            $qb->andWhere('m.firstname LIKE :search')
                ->setParameter('search', '%' . $params['search'] . '%');
        }

        return $qb->getQuery()->getResult();
    }
}