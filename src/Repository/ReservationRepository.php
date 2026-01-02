<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Reservation>
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function findConfirmedByDate(\DateTime $date): array
    {
        $start = (clone $date)->setTime(0, 0, 0);
        $end = (clone $date)->setTime(23, 59, 59);

        return $this->createQueryBuilder('r')
            // CORRECTION CRITIQUE : r.date_rdv (et non r.dateRdv)
            ->andWhere('r.date_rdv BETWEEN :start AND :end')
            ->andWhere('r.status != :cancelled')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('cancelled', 'CANCELED')
            // CORRECTION CRITIQUE : r.date_rdv
            ->orderBy('r.date_rdv', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
