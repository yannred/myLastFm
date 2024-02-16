<?php

namespace App\Entity;

use App\Repository\WidgetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WidgetRepository::class)]
class Widget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'widgets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?WidgetGrid $widgetGrid = null;

    #[ORM\Column(length: 255)]
    private ?string $code = null;

    #[ORM\Column(length: 1020, nullable: true)]
    private ?string $wording = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWidgetGrid(): ?WidgetGrid
    {
        return $this->widgetGrid;
    }

    public function setWidgetGrid(?WidgetGrid $widgetGrid): static
    {
        $this->widgetGrid = $widgetGrid;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getWording(): ?string
    {
        return $this->wording;
    }

    public function setWording(?string $wording): static
    {
        $this->wording = $wording;

        return $this;
    }
}
