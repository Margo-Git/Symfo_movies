<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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
        return $this->json($movies, 200, [], ['groups' => 'movies_get']);
    }

    /**
     * Get a movie by id
     * 
     * @Route("/api/movies/{id<\d+>}", name="api_movies_get_item", methods="GET")
     */
    public function show(Movie $movie): Response
    {
        // /!\ JSON Hijacking
        // @see https://symfony.com/doc/current/components/http_foundation.html#creating-a-json-response
        return $this->json($movie, Response::HTTP_OK, [], ['groups' => 'movies_get']);
    }

    /**
     * @Route("/api/movies", name="api_movies_post", methods="POST")
     */
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $jsonContent = $request->getContent();
        // dd($jsonContent);

        // on deserialise l'objet
        $movie = $serializer->deserialize($jsonContent, Movie::class, 'json');
        // dd($movie);

        // validation
        $errors = $validator->validate($movie);
        if (count($errors) > 0) {

            // on cast : on convertis notre variable en chaine
            // $errorsString = (string) $errors;
            // return new Response($errorsString);
            return $this->json(['errors' => $errors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // persit & flush
        $entityManager->persist($movie);
        $entityManager->flush();
        // dd($movie);

        // REST nous demande un status 201 et un header Location: url
        // return $this->redirectToRoute(
        //     'api_movies_get_item',
        //     ['id' => $movie->getid()],
        //     Response::HTTP_CREATED
        // );
        // REST nous demande un statut 201 et un header Location: url
        // Si on le fait "à la mano"
        return $this->json(
            // Le film que l'on retourne en JSON directement au front
            $movie,
            // Le status code
            // C'est cool d'utiliser les constantes de classe !
            // => ça aide à la lecture du code et au fait de penser objet
            Response::HTTP_CREATED,
            // Un header Location + l'URL de la ressource créée
            ['Location' => $this->generateUrl('api_movies_get_item', ['id' => $movie->getId()])],
            // Le groupe de sérialisation pour que $movie soit sérialisé sans erreur de référence circulaire
            ['groups' => 'movies_get']
        );
    }

    /**
     * @Route("/api/movies/{id<\d+>}", name="api_movies_put_item", methods={"PUT", "PATCH"})
     */
    public function itemEdit(Movie $movie = null, SerializerInterface $serializer, ValidatorInterface $validator, EntityManagerInterface $entityManager, Request $request): Response
    {

        // 404 Film non trouvé
        if ($movie === null) {
            return new JsonResponse(
                ["message" => "Film non trouvé"],
                Response::HTTP_NOT_FOUND
            );
        }

        // Récupère les données de la requête
        $data = $request->getContent();

        // @todo Pour PUT, s'assurer qu'on ait un certain nombre de champs
        // @todo Pour PATCH, s'assurer qu'on au moins un champ
        // sinon => 422 HTTP_UNPROCESSABLE_ENTITY

        // On désérialise le JSON vers *l'entité Movie existante*
        // @see https://symfony.com/doc/current/components/serializer.html#deserializing-in-an-existing-object
        $movie = $serializer->deserialize($data, Movie::class, 'json', [AbstractNormalizer::OBJECT_TO_POPULATE => $movie]);

        // On valide l'entité
        $errors = $validator->validate($movie);

        // Affichage des erreurs
        if (count($errors) > 0) {

            // Objectif : créer ce format de sortie
            // {
            //     "errors": {
            //         "title": [
            //             "Cette valeur ne doit pas être vide."
            //         ],
            //         "releaseDate": [
            //             "Cette valeur doit être de type string."
            //         ],
            //         "rating": [
            //             "Cette chaîne est trop longue. Elle doit avoir au maximum 1 caractère.",
            //             "Cette valeur doit être l'un des choix proposés."
            //         ]
            //     }
            // }

            // On va créer un joli tableau d'erreurs
            $newErrors = [];

            // Pour chaque erreur
            foreach ($errors as $error) {
                // Astuce ici ! on push dans un taleau
                // = similaire à la structure des Flash Messages
                // On push le message, à la clé qui contient la propriété
                $newErrors[$error->getPropertyPath()][] = $error->getMessage();
            }

            return new JsonResponse(["errors" => $newErrors], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        // Enregistrement en BDD
        $entityManager->flush();

        // @todo Conditionner le message de retour au cas où l'entité ne serait pas modifiée
        return new JsonResponse(["message" => "Film modifié"], Response::HTTP_OK);
    }


    /**
     * Delete a movie
     * 
     * @Route("/api/movies/{id<\d+>}", name="api_movies_delete", methods="DELETE")
     */
    public function delete(Movie $movie = null, EntityManagerInterface $em)
    {
        if (null === $movie) {

            $error = 'Ce film n\'existe pas';

            return $this->json(['error' => $error], Response::HTTP_NOT_FOUND);
        }

        $em->remove($movie);
        $em->flush();

        return $this->json(['message' => 'Le film a bien été supprimé.'], Response::HTTP_OK);
    }
}
