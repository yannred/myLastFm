<?php

namespace App\Entity;

use App\Repository\ImportRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImportRepository::class)]
class Import
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\Column(type: Types::DATETIME_MUTABLE)]
  private ?\DateTimeInterface $date = null;

  #[ORM\ManyToOne]
  private ?Scrobble $lastScrobble = null;

  #[ORM\ManyToOne(inversedBy: 'imports')]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $user = null;

  #[ORM\Column]
  private ?bool $successful = null;


  public function getId(): ?int
  {
    return $this->id;
  }

  public function getDate(): ?\DateTimeInterface
  {
    return $this->date;
  }

  public function setDate(\DateTimeInterface $date): static
  {
    $this->date = $date;

    return $this;
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

  public function setUser(?User $user): static
  {
    $this->user = $user;

    return $this;
  }

  public function getLastScrobble(): ?Scrobble
  {
    return $this->lastScrobble;
  }

  public function setLastScrobble(?Scrobble $lastScrobble): static
  {
    $this->lastScrobble = $lastScrobble;

    return $this;
  }

  public function isSuccessful(): ?bool
  {
      return $this->successful;
  }

  public function setSuccessful(bool $successful): static
  {
      $this->successful = $successful;

      return $this;
  }
}
