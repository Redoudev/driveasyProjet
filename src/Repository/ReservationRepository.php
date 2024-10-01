<?php

namespace App\Repository;

use App\Entity\Reservation;
use App\Entity\Voitures;
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
    public function conflictReservations(Voitures $voiture, \DateTimeInterface $dateDepart, \DateTimeInterface $dateRetour)
    {
        // Creation d'un QueryBuilder afin de voir si en bdd il y a déjà une résa sur la période
        return $this->createQueryBuilder('resa')
            ->where('resa.voiture = :voiture')
            ->andWhere(':dateDepart <= resa.date_retour AND :dateRetour >= resa.date_depart')
            ->setParameter('voiture', $voiture)
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
