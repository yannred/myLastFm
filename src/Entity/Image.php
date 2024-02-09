<?php

namespace App\Entity;

use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(nullable: true, options: ["default" => 0])]
    private ?int $size = null;

    #[ORM\Column(length: 1020)]
    private ?string $url = null;

    const SIZE_UNDEFINED = 0;
    const SIZE_SMALL = 1;
    const SIZE_MEDIUM = 2;
    const SIZE_LARGE = 3;
    const SIZE_EXTRA_LARGE = 4;

    const SIZES = [
        'undefined' => self::SIZE_UNDEFINED,
        'small' => self::SIZE_SMALL,
        'medium' => self::SIZE_MEDIUM,
        'large' => self::SIZE_LARGE,
        'extra-large' => self::SIZE_EXTRA_LARGE,
    ];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(?int $size): static
    {
        $this->size = $size;

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
}
