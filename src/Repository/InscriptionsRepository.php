<?php

namespace App\Repository;

use App\Entity\Inscriptions;
use App\Entity\Participants;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Inscriptions|null find($id, $lockMode = null, $lockVersion = null)
 * @method Inscriptions|null findOneBy(array $criteria, array $orderBy = null)
 * @method Inscriptions[]    findAll()
 * @method Inscriptions[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InscriptionsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Inscriptions::class);
    }

//Requete pour compter le nombre d'inscriptions à une sortie

    public function countByInscrits(Sortie $sortie)
    {
        return $this->createQueryBuilder('i')
            ->select('COUNT(i.id)')
            ->where('i.sortie = :sortie')
            ->setParameter(':sortie', $sortie)
            ->getQuery()
            ->getSingleScalarResult();

    }

    public function siDejaInscrit(Participants $participant, Sortie $sortie)
    {
        return $this->createQueryBuilder('i')
            ->where('i.participant = :participant')
            ->andWhere('i.sortie = :sortie')
            ->setParameter('participant', $participant)
            ->setParameter('sortie', $sortie)
            ->getQuery()
            ->getOneOrNullResult();


    }
    // /**
    //  * @return Inscriptions[] Returns an array of Inscriptions objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('i.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Inscriptions
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
