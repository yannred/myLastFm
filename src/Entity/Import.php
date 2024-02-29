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

  #[ORM\Column(options: ["default" => false])]
  private bool $started = false;

  #[ORM\Column(options: ["default" => false])]
  private bool $finalized = false;

  #[ORM\Column(options: ["default" => false])]
  private bool $error = false;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $errorMessage = null;

  #[ORM\Column(type: Types::BIGINT, nullable: true)]
  private ?string $totalScrobble = null;

  #[ORM\Column(type: Types::BIGINT, nullable: true)]
  private ?string $finalizedScrobble = null;


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

  public function isStarted(): bool
  {
    return $this->started;
  }

  public function setStarted(bool $started): static
  {
    $this->started = $started;

    return $this;
  }

  public function isFinalized(): bool
  {
      return $this->finalized;
  }

  public function setFinalized(bool $finalized): static
  {
      $this->finalized = $finalized;

      return $this;
  }

  public function isError(): bool
  {
    return $this->error;
  }

  public function setError(bool $error): static
  {
    $this->error = $error;

    return $this;
  }

  public function getErrorMessage(): ?string
  {
    return $this->errorMessage;
  }

  public function setErrorMessage(?string $errorMessage): static
  {
    $this->errorMessage = $errorMessage;

    return $this;
  }

  public function getTotalScrobble(): ?string
  {
      return $this->totalScrobble;
  }

  public function setTotalScrobble(?string $totalScrobble): static
  {
      $this->totalScrobble = $totalScrobble;

      return $this;
  }

  public function getFinalizedScrobble(): ?string
  {
      return $this->finalizedScrobble;
  }

  public function setFinalizedScrobble(?string $finalizedScrobble): static
  {
      $this->finalizedScrobble = $finalizedScrobble;

      return $this;
  }
}
