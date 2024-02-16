<?php

namespace App\Entity;

use App\Repository\WidgetRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
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

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $comment = null;

  #[ORM\Column]
  private ?int $typeWidget = null;

  #[ORM\Column(nullable: true)]
  private ?int $subTypeWidget = null;

  #[ORM\Column(type: Types::TEXT, nullable: true)]
  private ?string $query = null;

  #[ORM\Column(nullable: true)]
  private ?float $width = null;

  #[ORM\Column(nullable: true)]
  private ?float $height = null;

  #[ORM\Column(nullable: true)]
  private ?float $positionX = null;

  #[ORM\Column(nullable: true)]
  private ?float $positionY = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $fontColor = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $backgroundColor = null;

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

  public function getComment(): ?string
  {
    return $this->comment;
  }

  public function setComment(?string $comment): static
  {
    $this->comment = $comment;

    return $this;
  }

  public function getTypeWidget(): ?int
  {
    return $this->typeWidget;
  }

  public function setTypeWidget(int $typeWidget): static
  {
    $this->typeWidget = $typeWidget;

    return $this;
  }

  public function getSubTypeWidget(): ?int
  {
    return $this->subTypeWidget;
  }

  public function setSubTypeWidget(?int $subTypeWidget): static
  {
    $this->subTypeWidget = $subTypeWidget;

    return $this;
  }

  public function getQuery(): ?string
  {
    return $this->query;
  }

  public function setQuery(?string $query): static
  {
    $this->query = $query;

    return $this;
  }

  public function getWidth(): ?float
  {
    return $this->width;
  }

  public function setWidth(?float $width): static
  {
    $this->width = $width;

    return $this;
  }

  public function getHeight(): ?float
  {
    return $this->height;
  }

  public function setHeight(?float $height): static
  {
    $this->height = $height;

    return $this;
  }

  public function getPositionX(): ?float
  {
    return $this->positionX;
  }

  public function setPositionX(?float $positionX): static
  {
    $this->positionX = $positionX;

    return $this;
  }

  public function getPositionY(): ?float
  {
    return $this->positionY;
  }

  public function setPositionY(?float $positionY): static
  {
    $this->positionY = $positionY;

    return $this;
  }

  public function getFontColor(): ?string
  {
    return $this->fontColor;
  }

  public function setFontColor(?string $fontColor): static
  {
    $this->fontColor = $fontColor;

    return $this;
  }

  public function getBackgroundColor(): ?string
  {
    return $this->backgroundColor;
  }

  public function setBackgroundColor(?string $backgroundColor): static
  {
    $this->backgroundColor = $backgroundColor;

    return $this;
  }
}
