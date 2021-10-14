<?php

namespace App\Controller\Back;

use App\Repository\MovieRepository;
use App\Form\Movietype;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Movie;
use Symfony\Component\HttpFoundation\Request;

class MovieController extends AbstractController
{
    /**
     * Lister les films
     *
     * @Route("/back/movie/browse", name="back_movie_browse", methods={"GET"})
     */
    public function browse(MovieRepository $movieRepository)
    {
        $movies = $movieRepository->findAllOrderedByTitleAscQB();
        return $this->render('back/movie/browse.html.twig', [
            'movies' => $movies
        ]);
    }

    /**
     * Afficher un film
     *
     * @Route("/back/movie/read/{id}", name="back_movie_read", methods={"GET"})
     */
    public function read(Movie $movie = null)
    {
        if ($movie === null) {
            throw $this->createNotFoundException('Film non trouvé.');
        }

        return $this->render('back/movie/read.html.twig', [
            'movie' => $movie,
        ]);
    }

    /**
     * Ajouter un film
     *
     * @Route("/back/movie/add", name="back_movie_add", methods={"GET", "POST"})
     */
    public function add(Request $request)
    {
        $movie = new Movie();

        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On définit le slug du film depuis son titre
            // => transféré dans MovieListener

            $em = $this->getDoctrine()->getManager();
            $em->persist($movie);
            $em->flush();

            // $this->addFlash('success', $messageGenerator->getRandomMessage());

            return $this->redirectToRoute('back_movie_read', ['id' => $movie->getId()]);
        }

        // Affiche le form
        return $this->render('back/movie/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Editer un film
     * 
     * @Route("/back/movie/edit/{id}", name="back_movie_edit", methods={"GET","POST"})
     */
    public function edit()
    {
    }

    /**
     * Supprimer un film
     * => en GET à convertir en POST ou mieux en DELETE
     * 
     * @Route("/back/movie/delete/{id<\d+>}", name="back_movie_delete", methods={"GET"})
     */
    public function delete()
    {
    }
}