<?php

namespace App\DataFixtures;

use App\DataFixtures\Provider\MovieDbProvider;
use App\Entity\Genre;
use App\Entity\Movie;
use App\Entity\Person;
use App\Entity\Casting;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // notre fournisseur de données
        $movieDbProvider = new MovieDbProvider();

        // create 10 genre

        // liste des genres
        $genresList = [];

        for ($i = 0; $i <= 20; $i++) {

            $genre = new Genre();
            $genre->setName($movieDbProvider->movieGenre());

            // on stocke les genres pour association ultérieure
            $genresList[] = $genre;

            $manager->persist($genre);
        }

        // create 25 movies

        $moviesList = [];

        for ($i = 0; $i <= 25; $i++) {

            $movie = new Movie();
            $movie->setTitle($movieDbProvider->movieTitle());
            $movie->setCreatedAt(new DateTime());

            // Association de 3 genres random
            for ($r = 1; $r <= mt_rand(1, 3); $r++) {
                // array_rand va ramener un index au hasard dans le tableau des genres entre 0 et 20
                $movie->addGenre($genresList[array_rand($genresList)]);
            }

            // On stocke...
            $moviesList[] = $movie;

            $manager->persist($movie);
        }

        // create 20 persons
        $personsList = [];
        for ($i = 0; $i <= 20; $i++) {

            $person = new Person();
            $person->setFirstname('Prénom ' . $i);
            $person->setLastname('Nom ' . $i);

            $personsList[] = $person;

            $manager->persist($person);
        }
        
        // Les castings
        for ($i = 1; $i < 100; $i++) {
            $casting = new Casting();
            $casting->setRole('Rôle ' . $i);
            $casting->setCreditOrder(mt_rand(1, 10));

            // On va chercher un film au hasard dans la liste des films créée au-dessus
            // Variante avec mt_rand et count
            $randomMovie = $moviesList[mt_rand(0, count($moviesList) - 1)];
            $casting->setMovie($randomMovie);

            // On va chercher une personne au hasard dans la liste des personnes créée au-dessus
            // Variante avec array_rand()
            $randomPerson = $personsList[array_rand($personsList)];
            $casting->setPerson($randomPerson);

            // On persist
            $manager->persist($casting);
        }

        $manager->flush();
    }
}
