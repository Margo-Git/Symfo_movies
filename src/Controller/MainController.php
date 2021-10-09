<?php

namespace App\Controller;

use App\Repository\MovieRepository;
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
        $movies = $movieRepository->findBy(
            [],
            ['title' => 'ASC']
        );

        return $this->render('main/home.html.twig', [
            'movies' => $movies,
        ]);
    }
}
