<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    // Find all movies classée par titre asc version querybuilder
    public function findAllOrderedByTitleAscQB()
    {
        // on crée un objet de type query builder sur l'entité Movie
        // 'm' est alias pour l'entité Movie
        return $this->createQueryBuilder('m')
            // on classe par titre asc
            ->orderBy('m.title', 'ASC')
            // on ne veut pas limiter les résultats
            // ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    
    // même chose version DQL

    public function findAllOrderedByTitleAscDQL()
    {
         // C'est le Manager qui va nous permettre d'écrire une requête en DQL
         $entityManager = $this->getEntityManager();

         // En DQL, on précisé le FQCN (namespace + classe = App\Entity\Movie) de l'entité
         $query = $entityManager->createQuery(
             'SELECT m
             FROM App\Entity\Movie m
             ORDER BY m.title ASC'
         );
 
         // returns an array of Movie objects
         return $query->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Movie
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
