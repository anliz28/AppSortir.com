<?php

namespace App\Repository;

use App\Entity\Participants;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;



/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    function findSearch($filter, Participants $participants)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s')
            ->join('s.inscriptions', 'i');



        /*if (!empty($filter->organisateur)) {
            $query
                ->join('s.inscription', 'i')
        }*/

        // if(!empty($filter->recherche)) {
        // $query = $query
        //->andWhere('recherche= :recherche’)
        //  ->setParameter(':recherche',$filter->recherche);
        //  }

        /*if (!empty($filter->start)) {
            $query = $query
                ->andWhere('start= :start')
                ->setParameter(':start', $filter->start);
        }

        if (!empty($filter->end)) {
            $query = $query
                ->andWhere('s.end= :end')
                ->setParameter(':end', $filter->end);
        }


        if (!empty($filter->organisateur)) {
            $query = $query
                ->andWhere('s.organisateur= :organisateur)')
                ->setParameter('organisateur', $filter->organisateur);


            if (!empty($filter->inscrit)) {
                $query = $query
                    ->andWhere('s.inscrit = :inscrit')
                    ->setParameter('inscrit', $filter->inscrit);
            }

            if (!empty($filter->pasInscrit)) {
                $query = $query
                    ->andWhere('s.pasInscrit = :pasInscrit')
                    ->setParameter('pasInscrit', $filter->pasInscrit);
            }


            if (!empty($filter->sortiePassee)) {
                $query = $query
                    ->andWhere('s.sortiePassee = :sortiePassee')
                    ->setParameter('sortiePassee', $filter->sortiePassee);
            }
        }*/


        return $query->getQuery()->getResult();
    }




    function findSortieActives(UserInterface $user)
    {
        $dateDuJour = new \DateTime();
        $dateArchivage = $dateDuJour->modify("- 1 month");


        $query = $this->createQueryBuilder('s');
        $query->where('s.dateDebut > :dateArchivage');
        $query->andWhere('s.etat > 1');
        $query->orWhere('s.etat>0 and s.organisateur = :user');
        //$query->andWhere('s.organisateur = :user');
        $query->setParameter('dateArchivage', $dateArchivage);
        $query->setParameter('user', $user);
        $query = $query->getQuery();

        return $query->getResult();

}

    // /**
    //  * @return Sortie[] Returns an array of Sortie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Sortie
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

}

