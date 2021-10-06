<?php

namespace App\Controller;

use App\Entity\Movie;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * Tests BREAD
     * 
     * Browse =>
     * Read =>
     * Edit =>
     * Add =>
     * Delete =>
     * 
     * @Route("/test/add", name="test_add")
     */
    public function add(): Response
    {
        // 1. on crée une entité php
        $movie = new Movie();
        // 2. on renseigne l'entité
        $movie->setTitle('Dune');
        // date courante pour created at
        $movie->setCreatedAt(new DateTime());
        // 3. on demande au manager de doctrine de prendre l'entité
        // il se prépare à "persister l'entité
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($movie);
        // 4. puis de la sauvegarder en db (exécuter les requêtes sql necessaires, ici INSERT INTO)
        $entityManager->flush();

        return new Response('film ajouté : ' .$movie->getId() .'</body>');
        // ps: le </body> est rajouté pour que la toolbar s'affiche bien
    }

    /**
     * @Route("/test/browse", name="test_browse")
     */
    public function browse()
    {
        // pour accéder aux données de la table movie
        // on passe par le repository de l'entité Movie
        // ps: Movie::class => 'App\Entity\Movie
        $movieRepository = $this->getDoctrine()->getRepository(Movie::class);
        // on utilise les méthodes d'accès fournies par ce repository
        $movies = $movieRepository->findAll();
        dump($movies);
        return new Response('liste des films</body>');
    }

    /**
     * @Route("/test/read/{id<\d+>}", name="test_read")
     */
    public function read($id)
    {
        // pour accéder aux données de la table movie
        // on passe par le repository de l'entité Movie
        // ps: Movie::class => 'App\Entity\Movie
        $movieRepository = $this->getDoctrine()->getRepository(Movie::class);
        // on utilise les méthodes d'accès fournies par ce repository
        $movie = $movieRepository->find($id);
        dump($movie);
        return new Response('film n° : ' .$id .'</body>');
    }

    /**
     * @Route("/test/edit/{id<\d+>}", name="test_edit")
     */
    public function edit($id)
    {
        // on va cherche le film, on le modifie, et on le save
        $movieRepository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $movieRepository->find($id);

        // mise à jour
        $movie->setUpdatedAt(new DateTime());

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        return $this->redirectToRoute('test_read', ['id' => $id]);
    }

    /**
     * @Route("/test/delete/{id<\d+>}", name="test_delete")
     */
    public function delete($id)
    {
        // on va cherche le film, on le modifie, et on le save
        $movieRepository = $this->getDoctrine()->getRepository(Movie::class);
        $movie = $movieRepository->find($id);


        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($movie);
        $entityManager->flush();

        return $this->redirectToRoute('test_read', ['id' => $id]);
    }
}
