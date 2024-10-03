<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Voitures;
use App\Entity\Agence;
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

    /**
     * Trouver les réservations en conflit avec une période donnée
     */
    public function conflictReservations(Voitures $voiture, \DateTimeInterface $dateDepart, \DateTimeInterface $dateRetour, Agence $agence): array
    {
        return $this->createQueryBuilder('r')
            ->where('r.voiture = :voiture')
            ->andWhere('r.agence = :agence')
            ->andWhere('r.date_depart < :dateRetour')
            ->andWhere('r.date_retour > :dateDepart')
            ->setParameter('voiture', $voiture)
            ->setParameter('agence', $agence)
            ->setParameter('dateDepart', $dateDepart)
            ->setParameter('dateRetour', $dateRetour)
            ->getQuery()
            ->getResult();
    }


    //    /**
    //     * @return Reservation[] Returns an array of Reservation objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('r.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Reservation
    //    {
    //        return $this->createQueryBuilder('r')
    //            ->andWhere('r.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
