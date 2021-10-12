<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Repository\CastingRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * liste des films
     * 
     * @Route("/", name="home")
     */
    public function home(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAllOrderedByTitleAscDQL();

        return $this->render('main/home.html.twig', [
            'movies' => $movies,
        ]);
    }

    /**
     * Affiche un film
     *
     * @Route("movie/{id<\d+>}", name="movie_show")
     */
    public function movieShow(CastingRepository $castingRepository, Movie $movie = null)
    {
        // film non trouvé
        if ($movie === null) {
            throw $this->createNotFoundException('Film non trouvé');
        }

        // on utilise notre requête custom
        $castings = $castingRepository->findAllCastingsByMovieJoinedToPersonDQL($movie);

        
        return $this->render('main/movie_show.html.twig', [
            'movie' => $movie,
            'castings' => $castings,
        ]);
    }
}
