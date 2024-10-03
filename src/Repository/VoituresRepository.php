<?php

namespace App\Repository;

use App\Entity\Voitures;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Voitures>
 */
class VoituresRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voitures::class);
    }

    public function findUniqueVoitures(array $criteria = []): array
    {
        $qb = $this->createQueryBuilder('v');

        if (!empty($criteria['marque'])) {
            $qb->andWhere('v.marque = :marque')
                ->setParameter('marque', $criteria['marque']);
        }
        if (!empty($criteria['boite'])) {
            $qb->andWhere('v.boite = :boite')
                ->setParameter('boite', $criteria['boite']);
        }
        if (!empty($criteria['carburant'])) {
            $qb->andWhere('v.carburant = :carburant')
                ->setParameter('carburant', $criteria['carburant']);
        }

        $qb->groupBy('v.marque, v.modele, v.annee, v.couleur, v.boite, v.carburant, v.prix, v.image');

        return $qb->getQuery()->getResult();
    }

    /**
     * Récupérer les marques distinctes des voitures
     */
    public function findDistinctMarques(): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('DISTINCT v.marque')
            ->orderBy('v.marque', 'ASC');

        return array_column($qb->getQuery()->getResult(), 'marque');
    }

    /**
     * Récupérer les voitures filtrées par une marque spécifique
     */
    public function findByMarque(string $marque): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.marque = :marque')
            ->setParameter('marque', $marque)
            ->orderBy('v.marque', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupérer les boites de vitesse
     */
    public function findDistinctBoites(): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('DISTINCT v.boite')
            ->orderBy('v.boite', 'ASC');

        return array_column($qb->getQuery()->getResult(), 'boite');
    }

    /**
     * Récupérer les voitures filtrées par une boîte spécifique
     */
    public function findByBoite(string $boite): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.boite = :boite')
            ->setParameter('boite', $boite)
            ->orderBy('v.marque', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupérer les carburants
     */
    public function findDistinctCarburant(): array
    {
        $qb = $this->createQueryBuilder('v')
            ->select('DISTINCT v.carburant')
            ->orderBy('v.carburant', 'ASC');

        return array_column($qb->getQuery()->getResult(), 'carburant');
    }

    /**
     * Récupérer les voitures filtrées par carburant
     */
    public function findByCarburant(string $carburant): array
    {
        return $this->createQueryBuilder('v')
            ->where('v.carburant = :carburant')
            ->setParameter('carburant', $carburant)
            ->orderBy('v.marque', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
