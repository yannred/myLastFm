<?php

namespace App\Entity;

use App\Repository\WidgetGridRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WidgetGridRepository::class)]
class WidgetGrid
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  #[ORM\ManyToOne(inversedBy: 'widgetGrids')]
  #[ORM\JoinColumn(nullable: false)]
  private ?User $user = null;

  #[ORM\Column(length: 255, nullable: true)]
  private ?string $code = null;

  #[ORM\Column(length: 1020, nullable: true)]
  private ?string $wording = null;

  #[ORM\Column]
  private ?bool $defaultGrid = null;

  #[ORM\OneToMany(mappedBy: 'widgetGrid', targetEntity: Widget::class)]
  private Collection $widgets;

  public function __construct()
  {
    $this->widgets = new ArrayCollection();
  }

  public function getId(): ?int
  {
    return $this->id;
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

  public function isDefaultGrid(): ?bool
  {
    return $this->defaultGrid;
  }

  public function setDefaultGrid(bool $defaultGrid): static
  {
    $this->defaultGrid = $defaultGrid;

    return $this;
  }

  /**
   * @return Collection<int, Widget>
   */
  public function getWidgets(): Collection
  {
    return $this->widgets;
  }

  public function addWidget(Widget $widget): static
  {
    if (!$this->widgets->contains($widget)) {
      $this->widgets->add($widget);
      $widget->setWidgetGrid($this);
    }

    return $this;
  }

  public function removeWidget(Widget $widget): static
  {
    if ($this->widgets->removeElement($widget)) {
      // set the owning side to null (unless already changed)
      if ($widget->getWidgetGrid() === $this) {
        $widget->setWidgetGrid(null);
      }
    }

    return $this;
  }


}
