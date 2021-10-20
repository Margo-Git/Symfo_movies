<?php

namespace App\Command;

use App\Repository\MovieRepository;
use App\Service\OmdbApi;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Récupère les posters des films
 * - soit 1 film si titre donnée
 * - soit tous les films sinon
 */

class MoviePosterCommand extends Command
{
    // nom de la commande
    protected static $defaultName = 'app:movie:poster';
    // description de la commande
    protected static $defaultDescription = 'fetch movie posters from OMDB PI';

    // Les services nécessaires à notre commande...
    private $movieRepository;
    private $entityManager;
    private $omdbApi;

    /**
     * ... qu'on récupère en injection de dépendances ici
     */
    public function __construct(MovieRepository $movieRepository, EntityManagerInterface $entityManager, OmdbApi $omdbApi)
    {
        $this->movieRepository = $movieRepository;
        $this->entityManager = $entityManager;
        $this->omdbApi = $omdbApi;

        // On doit appeler le constructeur de la classe parent
        // si le parent ET l'enfant ont un constructeur (sinon le risque c'est qu'on l'écrase)
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            // argument = valeur à transmettre à la commande
            ->addArgument('title', InputArgument::OPTIONAL, 'movie title to fetch')
            // flag/modifier/otpion, qui change le comportement de la commande
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        // on récupère l'argument arg1 si présent
        $title = $input->getArgument('title');

        // si un titre est présent, on ne traite que ce film
        if ($title) {
            $io->note(sprintf('Movie to fetch: %s', $title));
            $movie = $this->movieRepository->findOneBy(['title' => $title]);
            // On colle $movie dans un tableau $movies (pour simplifier le foreach plus bas)
            $movies = [$movie];
        } else {
            // sinon on traite tous les films
            $io->note(sprintf('Fetching all movies'));
            $movies = $this->movieRepository->findAll();
        }

        if ($input->getOption('option1')) {
            // ...
        }

        // logique métier : objectif de la commande
        // on récupère les films concernés

        // dd($movies);

        // On boucle dessus, on va chercher les données associées sur OMDP API
        foreach ($movies as $movie) {
            // on envoie le titre du film à notre service OMDB API
            $moviePoster = $this->omdbApi->fetchPoster($movie->getTitle());
            // dd($movieData);
            // on met à jour d'url du poster dans le film
            $movie->setPoster($moviePoster);

        }

        // on flush donc on a besoin de entityManager
        $this->entityManager->flush();

        $io->success('All movies fetched');

        return Command::SUCCESS;
    }
}
