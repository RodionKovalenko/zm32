<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;

class UserRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getUserByParams(array $params = [])
    {
        $qb = $this->createQueryBuilder('u')
            ->orderBy('u.vorname', 'ASC')
            ->orderBy('u.lastname', 'ASC');

        if (isset($params['search'])) {
            $qb->andWhere('(u.firstname LIKE :search) OR (u.lastname LIKE :search)')
                ->setParameter('search', '%' . $params['search'] . '%');
        }
        if (isset($params['departmentIds'])) {
            $qb->leftJoin('u.mitarbeiter', 'm')
                ->leftJoin('m.mitarbeiterToDepartments', 'd')
                ->andWhere('d.department IN (:departmentIds)')
                ->setParameter('departmentIds', $params['departmentIds']);
        }

        return $qb->getQuery()->getResult();
    }
}