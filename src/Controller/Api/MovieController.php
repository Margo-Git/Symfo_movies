<?php

namespace App\Controller\Api;

use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MovieController extends AbstractController
{
    /**
     * Get movies collection
     * @Route("/api/movies", name="api_movies_get", methods="GET")
     */
    public function index(MovieRepository $movieRepository): Response
    {
        $movies = $movieRepository->findAll();
        // Symfony va sérialisée les données de l'entité sous form de json
        // on fourni les données dans un tableau, un statut 200, un tableau vide pour les headers, et le tableau de groupe
        return $this->json([$movies], 200, [], ['groups' => 'movies_get']);
    }
}
