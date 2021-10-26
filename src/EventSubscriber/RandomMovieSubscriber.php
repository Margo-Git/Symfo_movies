<?php

namespace App\EventSubscriber;

use App\Repository\MovieRepository;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Twig\Environment as Twig;

class RandomMovieSubscriber implements EventSubscriberInterface
{

    /**
     * On appelle le service MovieRepository
     */
    private $movieRepository;

    /**
     * Twig
     */
    private $twig;

    public function __construct(MovieRepository $movieRepository, Twig $twig)
    {
        $this->movieRepository = $movieRepository;
        $this->twig = $twig;
    }

    
    public function onKernelController(ControllerEvent $event)
    {

        // si requete api, on sort (pas de random movie si pas de html)
        if (preg_match('/^\/api/' , $event->getRequest()->getPathInfo())) {
            return;
        }
        
        // dump($event);
        

        // récupérer le controller
        $controller = $event->getController();

        if (is_array($controller)) {
            $controller = $controller[0];
        }

        // on récupère le nom de la class de controller
        $controllerClassName = (get_class($controller));
        // dump($controllerClassName);

        if (strpos($controllerClassName, 'App\Controller') === false) {
            return;
        }

    

        // 2. On va chercher un film au hasard
        // @todo Utiliser ORDER BY RAND() LIMIT 1
        // dans une requête custom dans le Respository

        // En attendant, on va faire un shuffle() sur tous les films
        $movies = $this->movieRepository->findAll();
        // On mélange, on prend le premier
        shuffle($movies);
        $randomMovie = $movies[0];
        // dump($randomMovie);

        // 3. On le transmet à Twig
        $this->twig->addGlobal('randomMovie', $randomMovie);

    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }
}
