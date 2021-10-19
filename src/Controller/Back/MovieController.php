<?php

namespace App\Controller\Back;

use App\Entity\Movie;
use App\Form\MovieType;
use App\Service\MessageGenerator;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
     * @Route("/back/movie/read/{id<\d+>}", name="back_movie_read", methods={"GET"})
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
            $this->addFlash('success', 'Movie added.');

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
     * @Route("/back/movie/edit/{id<\d+>}", name="back_movie_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Movie $movie = null, MessageGenerator $messageGenerator)
    {
        // 404 ?
        if ($movie === null) {
            throw $this->createNotFoundException('Film non trouvé.');
        }

        $form = $this->createForm(MovieType::class, $movie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // On définit le slug du film depuis son titre
            // /!\ SEO : il faudra prévoir un système de redirection
            // de l'ancienne URL vers la nouvelle URL (avec un status 302)
            // => transféré dans MovieListener

            $em = $this->getDoctrine()->getManager();
            // Pas de persist() pour un edit
            $em->flush();

            // $this->addFlash('success', $messageGenerator->getRandomMessage());
            $this->addFlash('success', $messageGenerator->getRandomMessage());

            return $this->redirectToRoute('back_movie_read', ['id' => $movie->getId()]);
        }

        // si pas en post, affiche le form
        return $this->render('back/movie/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Supprimer un film
     * => en GET à convertir en POST ou mieux en DELETE
     * 
     * @Route("/back/movie/delete/{id<\d+>}", name="back_movie_delete", methods={"GET"})
     */

    // movie = null ici
    public function delete(Movie $movie = null, EntityManagerInterface $entityManager)
    {
        // 404 ?
        // Conditions Yoda
        // @link https://fr.wikipedia.org/wiki/Condition_Yoda
        if (null === $movie) {
            throw $this->createNotFoundException("Le film n'existe pas.");
        }

        $entityManager->remove($movie);
        $entityManager->flush();

        $this->addFlash('success', 'Movie deleted.');
        // $this->addFlash('success', $messageGenerator->getRandomMessage());

        return $this->redirectToRoute('back_movie_browse');
    }
}