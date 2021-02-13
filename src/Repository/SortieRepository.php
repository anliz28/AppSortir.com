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
    }

    function findSortieActives(UserInterface $user)
    {
        $dateDuJour = new \DateTime();
        $dateArchivage = $dateDuJour->modify("- 1 month");


            $query = $this->createQueryBuilder('s');
              $query->where('s.dateDebut > :dateArchivage' );
              $query->andWhere('s.etat > 1');
              $query->orWhere('s.etat>0 and s.organisateur = :user');
              //$query->andWhere('s.organisateur = :user');
              $query->setParameter('dateArchivage',$dateArchivage);
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