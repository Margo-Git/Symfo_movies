<?php

namespace App\Service;

use Symfony\Component\String\Slugger\SluggerInterface;

class MySlugger
{
  // On a besoin du service sluger de symfo
  private $slugger;

  // variable est ce qu'on passe en minuscule ?

  private $isLower;

  // Injection des services nécessaires
  public function __construct(SluggerInterface $slugger, bool $isLower)
  {
    $this->slugger = $slugger;
    $this->isLower = $isLower;
  }

  /**
   * Slugifie une chaine de données
   * 
   * @param string la chaine à slugifier
   * @return string la chaine slugifieé
   */
  public function slugify(string $stringToSlug): string
  {
    if ($this->isLower) {
      $slug = $this->slugger->slug($stringToSlug)->lower();
    } else {
      $slug = $this->slugger->slug($stringToSlug);
    }

    return $slug;
  }
}
