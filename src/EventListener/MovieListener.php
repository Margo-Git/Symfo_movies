<?php

// https://symfony.com/doc/current/doctrine/events.html#doctrine-entity-listeners
namespace App\EventListener;

use App\Entity\Movie;
use App\Service\MySlugger;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class MovieListener
{

    private $mySlugger;

    public function __construct(MySlugger $mySlugger)
    {
        $this->mySlugger = $mySlugger;
    }
    // the entity listener methods receive two arguments:
    // the entity instance and the lifecycle event
    public function slugify(Movie $movie, LifecycleEventArgs $event): void
    {
        // on veut slugifier le titre => on a besoin de notre service mySlugger
        $movie->setSlug($this->mySlugger->slugify($movie->getTitle()));
    }
}