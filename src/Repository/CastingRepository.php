<?php

namespace App\Repository;

use App\Entity\Casting;
use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Casting|null find($id, $lockMode = null, $lockVersion = null)
 * @method Casting|null findOneBy(array $criteria, array $orderBy = null)
 * @method Casting[]    findAll()
 * @method Casting[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CastingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Casting::class);
    }


    /**
     * Tous les castings d'un film donné
     * joints sur l'entité Person
     * 
     * SELECT * FROM `casting`
     * INNER JOIN `person` ON `casting`.`person_id` = `person`.`id`
     * WHERE `movie_id` = 7
     */
    public function findAllCastingsByMovieJoinedToPersonDQL(Movie $movie)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT c, p
            -- je veux récupérer des données de person depuis l\'entité casting
            FROM App\Entity\Casting c
            INNER JOIN c.person p
            WHERE c.movie = :movie
            ORDER BY c.creditOrder'
        )->setParameter('movie', $movie);

        return $query->getResult();
    }
    

}
