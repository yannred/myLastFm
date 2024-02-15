<?php

namespace App\Entity;

use App\Repository\TrackRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TrackRepository::class)]
class Track
{

  const LIMIT_TOP_TRACKS = 4;

  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(length: 255)]
  private ?string $mbid = null;

  #[ORM\Column(length: 1020)]
  private ?string $name = null;

  #[ORM\Column(length: 1020)]
  private ?string $url = null;

  #[ORM\ManyToOne(inversedBy: 'tracks')]
  private ?Artist $artist = null;

  #[ORM\ManyToOne(inversedBy: 'tracks')]
  private ?Album $album = null;

  #[ORM\ManyToMany(targetEntity: Image::class)]
  private Collection $image;

  #[ORM\OneToMany(mappedBy: 'track', targetEntity: Scrobble::class, orphanRemoval: true)]
  private Collection $scrobbles;

  private ?string $userPlaycount = null;

  public function __construct()
  {
    $this->image = new ArrayCollection();
    $this->scrobbles = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getMbid(): ?string
  {
    return $this->mbid;
  }

  public function setMbid(string $mbid): static
  {
    $this->mbid = $mbid;

    return $this;
  }

  public function getName(): ?string
  {
    return $this->name;
  }

  public function setName(string $name): static
  {
    $this->name = $name;

    return $this;
  }

  public function getUrl(): ?string
  {
    return $this->url;
  }

  public function setUrl(string $url): static
  {
    $this->url = $url;

    return $this;
  }

  public function getArtist(): ?Artist
  {
    return $this->artist;
  }

  public function setArtist(?Artist $artist): static
  {
    $this->artist = $artist;

    return $this;
  }

  public function getAlbum(): ?Album
  {
    return $this->album;
  }

  public function setAlbum(?Album $album): static
  {
    $this->album = $album;

    return $this;
  }

  /**
   * @return Collection<int, Image>
   */
  public function getImage(): Collection
  {
    return $this->image;
  }

  public function addImage(Image $image): static
  {
    if (!$this->image->contains($image)) {
      $this->image->add($image);
    }

    return $this;
  }

  public function removeImage(Image $image): static
  {
    $this->image->removeElement($image);

    return $this;
  }

  /**
   * @return Collection<int, Scrobble>
   */
  public function getScrobbles(): Collection
  {
    return $this->scrobbles;
  }

  public function addScrobble(Scrobble $scrobble): static
  {
    if (!$this->scrobbles->contains($scrobble)) {
      $this->scrobbles->add($scrobble);
      $scrobble->setTrack($this);
    }

    return $this;
  }

  public function removeScrobble(Scrobble $scrobble): static
  {
    if ($this->scrobbles->removeElement($scrobble)) {
      // set the owning side to null (unless already changed)
      if ($scrobble->getTrack() === $this) {
        $scrobble->setTrack(null);
      }
    }

    return $this;
  }

  public function getUserPlaycount(): ?string
  {
    return $this->userPlaycount;
  }

  public function setUserPlaycount(?string $userPlaycount): static
  {
    $this->userPlaycount = $userPlaycount;

    return $this;
  }
}
