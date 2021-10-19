<?php

namespace App\Service;

use Psr\Log\LoggerInterface;

class MessageGenerator
{

  private $logger;

  // Les message doit-il être random ?
  private $isRandom;

  // Injection de service symfo dans notre service
  // on doit configurer la valeur de $isRandom, dans service.yaml
  public function __construct(LoggerInterface $logger, bool $isRandom)
  {
    // on va pouvoir logger des choses \o/ trop cool x)
    $this->logger = $logger;

    // Message aléatoire ou pas ?
    $this->isRandom = $isRandom;
  }


  private $messages = [
    'You did it! You updated the system! Amazing!',
    'That was one of the coolest updates I\'ve seen all day!',
    'Great work! Keep going!',
  ];

  public function getRandomMessage()
  {

    if ($this->isRandom) {
      $message = $this->messages[array_rand($this->messages)];
    } else {
      $message = 'action success';
    }
    

    // Pour l'exemple, on log es informations
    $this->logger->info('Random message', [
      'message' => $message,
    ]);

    return $message;
  }
}
