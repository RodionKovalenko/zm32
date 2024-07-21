<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Order;
use Doctrine\Common\Proxy\Proxy;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

abstract class DefaultRepository extends ServiceEntityRepository
{

    /**
     * Gibt die Entity mit der uebergebenen ID zurueck oder wirft eine EntityNotFoundException.
     *
     * @param int $entityId ID
     *
     * @throws EntityNotFoundException
     *
     * @return object Entity-Instanz
     */
    public function findOrThrowNotFound($entityId)
    {
        if ($entityId === null) {
            throw new EntityNotFoundException($this->getClassName() . ': null');
        }

        $entity = $this->find($entityId);

        if (empty($entity)) {
            throw new EntityNotFoundException($this->getClassName() . ': ' . $entityId);
        }

        return $entity;
    }

    /**
     * Gibt die Entity mit der uebergebenen ID zurueck oder wirft eine Exception, wenn nicht vorhanden.
     * Ist $entityId empty, wird eine neue Instanz angelegt.
     *
     * @param int         $entityId        ID
     * @param string|null $entityClassname Entity-Klassenname; wenn nicht angegeben, die Entity-Klasse des Repositories
     *
     * @throws \LogicException
     * @throws EntityNotFoundException
     *
     * @return object Entity-Instanz
     */
    public function findOrCreate($entityId, $entityClassname = null)
    {
        $entity = null;
        try {
            $entity = $this->find($entityId);
        } catch (\Exception) {
            // $entity bleibt null
        }

        if (empty($entity)) {
            if (!empty($entityId)) {
                throw new EntityNotFoundException($this->getClassName() . ': ' . $entityId);
            }

            $entityClassname ??= $this->getClassName();
            if (!class_exists($entityClassname)) {
                throw new \LogicException('Entity class not found: ' . $entityClassname);
            }
            $entity = new $entityClassname();
        }

        return $entity;
    }

    /**
     * Gibt die Entity mit der uebergebenen ID zurueck oder wirft eine AccessDeniedException.
     *
     * @param int $entityId ID
     *
     * @throws AccessDeniedException
     *
     * @return object Entity-Instanz
     */
    public function findOrThrowAccessDenied($entityId)
    {
        $entity = null;
        if ($entityId === null) {
            throw new AccessDeniedException($this->getClassName() . ': null');
        }

        try {
            $entity = $this->find($entityId);
        } catch (\Exception) {
            // $entity bleibt null
        }

        if (empty($entity)) {
            throw new AccessDeniedException($this->getClassName() . ': ' . $entityId);
        }

        return $entity;
    }

    /**
     * Gibt die Entity mit den uebergebenen Kritieren zurueck oder wirft eine EntityNotFoundException.
     *
     * @throws EntityNotFoundException
     *
     * @return object Entity-Instanz
     */
    public function findOneByOrThrowNotFound(array $criteria)
    {
        $entities = null;
        try {
            $entities = $this->findOneBy($criteria);
        } catch (\Exception) {
            // $entity bleibt null
        }

        if (empty($entities)) {
            throw new EntityNotFoundException($this->getClassName());
        }

        return $entities;
    }

    /**
     * Gibt die Entity mit den uebergebenen Kritieren zurueck oder wirft eine AccessDeniedException.
     *
     * @throws AccessDeniedException
     *
     * @return object Entity-Instanz
     */
    public function findOneByOrThrowAccessDenied(array $criteria)
    {
        $entities = null;
        try {
            $entities = $this->findOneBy($criteria);
        } catch (\Exception) {
            // $entity bleibt null
        }

        if (empty($entities)) {
            throw new AccessDeniedException($this->getClassName());
        }

        return $entities;
    }

    /**
     * Gibt Entities mit den uebergebenen Kritieren zurueck oder wirft eine EntityNotFoundException.
     *
     * @param int|null $limit
     * @param int|null $offset
     *
     * @throws EntityNotFoundException
     *
     * @return array Entities
     */
    public function findByOrThrowNotFound(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $entities = null;
        try {
            $entities = $this->findBy($criteria, $orderBy, $limit, $offset);
        } catch (\Exception) {
            // $entity bleibt null
        }

        if (empty($entities)) {
            throw new EntityNotFoundException($this->getClassName());
        }

        return $entities;
    }

    /**
     * Gibt Entities mit den uebergebenen Kritieren zurueck oder wirft eine AccessDeniedException.
     *
     * @param int|null $limit
     * @param int|null $offset
     *
     * @throws AccessDeniedException
     *
     * @return array Entities
     */
    public function findByOrThrowAccessDenied(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $entities = null;
        try {
            $entities = $this->findBy($criteria, $orderBy, $limit, $offset);
        } catch (\Exception) {
            // $entity bleibt null
        }

        if (empty($entities)) {
            throw new AccessDeniedException($this->getClassName());
        }

        return $entities;
    }

    /**
     * Zaehlt alle Datensaetze, die zu den uebergebenen Kriterien passen.
     */
    public function countBy(array $criteria): int
    {
        $persister = $this->_em->getUnitOfWork()->getEntityPersister($this->_entityName);

        return $persister->count($criteria);
    }

    /**
     * Speichert die Entity.
     *
     * @param object $entity
     */
    public function save($entity): void
    {
        $em = $this->getEntityManager();
        $em->persist($entity);
        $em->flush();
    }

    /**
     * Cleart den Entity Manager.

     */
    public function clearAll(): void
    {
        $this->getEntityManager()->clear();
    }

    /**
     * Speichert ein Array von Entities.
     *
     * @param array|iterable $entities

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
     */
    public function remove($entity): void
    {
        $em = $this->getEntityManager();
        $em->remove($entity);
        $em->flush();
    }

    public function detach(object $entity): void
    {
        $this->getEntityManager()->detach($entity);
    }

    public function detachAll(?array $entities): void
    {
        if (empty($entities)) {
            return;
        }
        $em = $this->getEntityManager();
        foreach ($entities as $entity) {
            $em->detach($entity);
        }
    }

    /**
     * Entfernt alle Entities im uebergebenen Array.
     *
     * @param array|\Traversable $entities
     *
     */
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

    /**
     * Beginnt eine Transaktion.
     */
    public function beginTransaction(): void
    {
        $this->getEntityManager()->beginTransaction();
    }

    /**
     * Commitet die aktuelle Transaktion.
     */
    public function commit(): void
    {
        $this->getEntityManager()->commit();
    }

    /**
     * Fuehrt einen Rollback der aktuellen Transaktion durch.
     */
    public function rollback(): void
    {
        $this->getEntityManager()->rollback();
    }

    /**
     * Liefert true, wenn der EntityManager offen ist.
     */
    public function isEntityManagerOpen(): bool
    {
        return $this->getEntityManager()->isOpen();
    }

    public function isTransactionActive(): bool
    {
        return $this->getEntityManager()->getConnection()->isTransactionActive();
    }

    /**
     * Liefert eine Referenz auf die Entity mit der uebergebenen ID.
     *
     *
     * @return bool|Proxy|object|null
     */
    public function getRef($id)
    {
        return $this->getEntityManager()->getReference($this->getClassName(), $id);
    }

    /**
     * Liefert die Original-Daten der Entity, wie Sie in der DB gespeichert sind (vor Aenderungen).
     *
     * @param object $entity
     *
     * @author Christopher Menke <christopher.menke@npo-applications.de>
     */
    public function getOriginalEntityData($entity): array
    {
        return $this->getEntityManager()->getUnitOfWork()->getOriginalEntityData($entity);
    }

    public function getChangedEntityData($entity): array
    {
        $unitOfWork = $this->getEntityManager()->getUnitOfWork();
        $unitOfWork->computeChangeSets();
        return $unitOfWork->getEntityChangeSet($entity);
    }

    public function lock($entity, int $lockMode, int $lockVersion = null): void
    {
        $this->getEntityManager()->lock($entity, $lockMode, $lockVersion);
    }

    public function findAllOrderedBy($field, $order = Order::Ascending->value)
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.' . $field, $order)
            ->getQuery()
            ->getResult();
    }
}