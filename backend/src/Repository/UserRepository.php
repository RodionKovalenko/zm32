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
        $qb = $this->createQueryBuilder('u');

        if (isset($params['search'])) {
            $qb->andWhere('u.name LIKE :search')
                ->setParameter('search', '%' . $params['search'] . '%');
        }

        if (isset($params['departmentIds'])) {
            $qb->join('u.department', 'd')
                ->andWhere('d.id IN (:departmentIds)')
                ->setParameter('departmentIds', $params['departmentIds']);
        }

        return $qb->getQuery()->getResult();
    }
}