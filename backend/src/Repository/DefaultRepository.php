<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMInvalidArgumentException;

abstract class DefaultRepository extends ServiceEntityRepository
{
    /**
     * Speichert die Entity.
     *
     * @param object $entity
     *
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function save($entity): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Cleart den Entity Manager.
     *
     * @throws ORMInvalidArgumentException
     */
    public function clearAll(): void
    {
        $this->getEntityManager()->clear();
    }

    /**
     * Speichert ein Array von Entities.
     *
     * @param array|iterable $entities
     *
     * @throws ORMInvalidArgumentException
     */
    public function saveAll($entities): void
    {
        $em = $this->getEntityManager();
        if (empty($entities)) {
            return;
        }
        foreach ($entities as $entity) {
            $em->persist($entity);
        }
        $em->flush();
    }

    /**
     * Entfernt die Entity.
     *
     * @param object $entity
     *
     * @throws ORMInvalidArgumentException
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function remove($entity): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    public function removeAll($entities): void
    {
        if (empty($entities)) {
            return;
        }
        $em = $this->getEntityManager();
        foreach ($entities as $entity) {
            $em->remove($entity);
        }
        $em->flush();
    }
}