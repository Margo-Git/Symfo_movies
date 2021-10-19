<?php

namespace App\Controller\Front;

use App\Entity\Movie;
use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\MovieRepository;
use App\Repository\CastingRepository;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * liste des films
     * 
     * @Route("/", name="home")
     */
    public function home(MovieRepository $movieRepository, GenreRepository $genreRepository): Response
    {
        $movies = $movieRepository->findAllOrderedByTitleAscDQL();
        $genres = $genreRepository->findBy([], ['name' => 'ASC']);

        return $this->render('front/main/home.html.twig', [
            'movies' => $movies,
            'genres' => $genres,
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

        
        return $this->render('front/main/movie_show.html.twig', [
            'movie' => $movie,
            'castings' => $castings,
        ]);
    }

    /**
     * Ajout d'une crituque sur un film
     *
     * @Route("/movie/{id}/add/review", name="movie_add_review")
     */
    public function movieAddReview(Movie $movie = null, Request $request)
    {
        // film non trouvé
        if ($movie === null) {
            throw $this->createNotFoundException('Film non trouvé');
        }

        // Nouvelle critique
        $review = new Review();

        // création du form
        $form = $this->createForm(ReviewType::class, $review);

        // prendre en charge la requête
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            // relation review <> movie
            $review->setMovie($movie);

            // on sauve la review
            $em = $this->getDoctrine()->getManager();
            $em->persist($review);
            $em->flush();

            return $this->redirectToRoute('front/movie_show', ['id' => $movie->getId()]);
        }

        // affichage du form
        return $this->render('front/main/movie_add_review.html.twig', [
            'form' => $form->createView(),
            'movie' => $movie,
        ]);
    }
}
