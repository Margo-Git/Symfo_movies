<?php

namespace App\Entity;

// on va appliquer la logique de mapping via l'annotation @ORM
// qui correspond à une dossier 'mapping' de Doctrine
use Doctrine\ORM\Mapping as ORM;

// cette classe Movie est une entité doctrine

/**
 * @ORM\Entity
 */
class Movie
{
  /**
   * Ceci est un doc block (donc utile pour nous seulement)
   * 
   * clé primaire
   * Auto-increment
   * type INT
   * 
   * ceci sont des annotations (donc utile pour doctrine)
   * 
   * @ORM\Id
   * @ORM\GeneratedValue
   * @ORM\Column(type="integer")
   */
  private $id;

  /**
   * Titre
   * 
   * @ORM\Column(type="string", length=211)
   */
  private $title;

  /**
   * Get clé primaire
   */ 
  public function getId()
  {
    return $this->id;
  }


  /**
   * Get titre
   */ 
  public function getTitle()
  {
    return $this->title;
  }

  /**
   * Set titre
   *
   * @return  self
   */ 
  public function setTitle($title)
  {
    $this->title = $title;

    return $this;
  }
}
