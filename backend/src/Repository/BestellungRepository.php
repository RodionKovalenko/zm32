<?php

namespace App\Repository;

use App\Entity\Bestellung;
use App\Entity\DepartmentTyp;
use Doctrine\Common\Collections\Order;
use Doctrine\Persistence\ManagerRegistry;

class BestellungRepository extends DefaultRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bestellung::class);
    }

    public function getByDepartment(array $filterParams = [])
    {
        $isAllDepartment = false;
        $deparments = $filterParams['departments'] ?? [];
        $status = $filterParams['status'] ?? [];
        $createdAfter = $filterParams['createdAfter'] ?? [];
        $datumBis = $filterParams['datumBis'] ?? [];
        $search = $filterParams['search'] ?? null;

        foreach ($deparments as $department) {
            if ($department->getTyp() === DepartmentTyp::ALLE->value) {
                $isAllDepartment = true;
                break;
            }
        }

        $q = $this->createQueryBuilder('b')
            ->leftJoin('b.departments', 'd');

        if (!$isAllDepartment) {
            $q->where('d.id IN (:departmentId)')
                ->setParameter('departmentId', $deparments);
        }

        if (!empty($status)) {
            $q->andWhere('b.status IN (:status)')
                ->setParameter('status', $status);
        }
        if (!empty($createdAfter)) {
            if (!($createdAfter instanceof \DateTimeInterface)) {
                $createdAfter = new \DateTime($createdAfter);
                $createdAfter->setTime(0, 0);
            }
            $q->andWhere('b.datum >= :createdAfter')
                ->setParameter('createdAfter', $createdAfter);
        }
        if (!empty($datumBis)) {
            if (!($datumBis instanceof \DateTimeInterface)) {
                $datumBis = new \DateTime($datumBis);
                $datumBis->setTime(23, 59, 59);
            }
            $q->andWhere('b.datum <= :datumBis')
                ->setParameter('datumBis', $datumBis);
        }
        if (!empty($search)) {
            $q->leftJoin('b.artikels', 'a')
                ->leftJoin('a.artikelToHerstRefnummers', 'hrn')
                ->leftJoin('a.artikelToLieferantBestellnummers', 'lbn')
                ->leftJoin('a.herstellers', 'h')
                ->leftJoin('a.lieferants', 'l')
                ->andWhere(
                    '((a.name LIKE :searchWord)
                 OR (h.name LIKE :searchWord)
                 OR (a.description LIKE :searchWord)
                   OR (b.description LIKE :searchWord)
                   OR (b.descriptionZusatz LIKE :searchWord)
                   OR (b.preis LIKE :searchWord)
                   OR (b.amount LIKE :searchWord)
                 OR (l.name LIKE :searchWord)
                  OR (hrn.refnummer LIKE :searchWord)
                   OR (lbn.bestellnummer LIKE :searchWord))'
                )
                ->setParameter('searchWord', '%' . $search . '%');
        }

        return $q->orderBy('d.name', Order::Ascending->value)
            ->getQuery()
            ->getResult();
    }
}