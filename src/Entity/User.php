<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string|null The hashed password
     */
    #[ORM\Column]
    //TODO : Encrypt password
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $userName = null;

    #[ORM\Column(length: 255)]
    //TODO : Convert in base64
    private ?string $lastFmApiKey = null;

    #[ORM\Column(length: 255)]
    //TODO : Convert in base64
    private ?string $lastFmApiSessionKey = null;

    #[ORM\Column(length: 255)]
    //TODO : Convert in base64
    private ?string $lasFmApiSecret = null;

    #[ORM\Column(length: 255)]
    //TODO : Convert in base64
    private ?string $lastFmUserName = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserName(): ?string
    {
        return $this->userName;
    }

    public function setUserName(string $userName): static
    {
        $this->userName = $userName;

        return $this;
    }

    public function getLastFmApiKey(): ?string
    {
        return $this->lastFmApiKey;
    }

    public function setLastFmApiKey(string $lastFmApiKey): static
    {
        $this->lastFmApiKey = $lastFmApiKey;

        return $this;
    }

    public function getLastFmApiSessionKey(): ?string
    {
        return $this->lastFmApiSessionKey;
    }

    public function setLastFmApiSessionKey(string $lastFmApiSessionKey): static
    {
        $this->lastFmApiSessionKey = $lastFmApiSessionKey;

        return $this;
    }

    public function getLasFmApiSecret(): ?string
    {
        return $this->lasFmApiSecret;
    }

    public function setLasFmApiSecret(string $lasFmApiSecret): static
    {
        $this->lasFmApiSecret = $lasFmApiSecret;

        return $this;
    }

    public function getLastFmUserName(): ?string
    {
        return $this->lastFmUserName;
    }

    public function setLastFmUserName(string $lastFmUserName): static
    {
        $this->lastFmUserName = $lastFmUserName;

        return $this;
    }
}
