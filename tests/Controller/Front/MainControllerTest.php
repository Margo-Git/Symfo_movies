<?php

namespace App\Tests\Controller\Front;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MainControllerTest extends WebTestCase
{
    /**
     * Test de la Home
     */
    public function testHome(): void
    {
        // crée un client http
        $client = static::createClient();
        // Envoie une requête vers l'url
        $crawler = $client->request('GET', '/');

        // bien statut 2xx ?
        $this->assertResponseIsSuccessful();
        // bien sur la page d'accueil ?
        $this->assertSelectorTextContains('h1', 'Tous les films');
    }

    /**
     * Visiteur anonyme n'a pas asscès à l'écriture d'une review
     * et se trouve redirigé
     */
    public function testReviewAddFailure()
    {
        // Crée un client HTTP
        $client = static::createClient();
        // Envoie une requête vers l'url '/'
        $crawler = $client->request('GET', '/movie/1/add/review');
        // Si form dans la page show :
        // $crawler = $client->request('POST', '/movie/rambo-2');

        $this->assertResponseRedirects();
    }
}
