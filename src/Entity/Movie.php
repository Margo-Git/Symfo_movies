<?php

namespace App\Entity;

// on va appliquer la logique de mapping via l'annotation @ORM
// qui correspond à une dossier 'mapping' de Doctrine

use DateTime;
use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

// cette classe Movie est une entité doctrine

/**
 * @ORM\Entity(repositoryClass=MovieRepository::class)
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
   * @ORM\Column(type="datetime")
   */
  private $createdAt;

  /**
   * @ORM\Column(type="datetime", nullable=true)
   */
  private $updatedAt;

  /**
   * @ORM\ManyToMany(targetEntity=Genre::class, inversedBy="movies")
   */
  private $genres;

  /**
   * @ORM\OneToMany(targetEntity=Casting::class, mappedBy="movie")
   * @ORM\OrderBy({"creditOrder"="ASC"})
   */
  private $castings;

  public function __construct()
  {
      $this->genres = new ArrayCollection();
      $this->castings = new ArrayCollection();
  }

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
  public function setTitle(string $title)
  {
    $this->title = $title;

    return $this;
  }

  /**
   * Get the value of createdAt
   */ 
  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  /**
   * Set the value of createdAt
   *
   * @return  self
   */ 
  public function setCreatedAt(DateTime $createdAt)
  {
    $this->createdAt = $createdAt;

    return $this;
  }

  /**
   * Get the value of updatedAt
   */ 
  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }

  /**
   * Set the value of updatedAt
   *
   * @return  self
   */ 
  public function setUpdatedAt(DateTime $updatedAt)
  {
    $this->updatedAt = $updatedAt;

    return $this;
  }

  /**
   * @return Collection|Genre[]
   */
  public function getGenres(): Collection
  {
      return $this->genres;
  }

  public function addGenre(Genre $genre): self
  {
      if (!$this->genres->contains($genre)) {
          $this->genres[] = $genre;
      }

      return $this;
  }

  public function removeGenre(Genre $genre): self
  {
      $this->genres->removeElement($genre);

      return $this;
  }

  /**
   * @return Collection|Casting[]
   */
  public function getCastings(): Collection
  {
      return $this->castings;
  }

  public function addCasting(Casting $casting): self
  {
      if (!$this->castings->contains($casting)) {
          $this->castings[] = $casting;
          $casting->setMovie($this);
      }

      return $this;
  }

  public function removeCasting(Casting $casting): self
  {
      if ($this->castings->removeElement($casting)) {
          // set the owning side to null (unless already changed)
          if ($casting->getMovie() === $this) {
              $casting->setMovie(null);
          }
      }

      return $this;
  }
}
