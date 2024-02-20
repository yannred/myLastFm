<?php

namespace App\Data\Widget;

use App\Entity\WidgetGrid;

abstract class WidgetModel
{
  protected ?int $id = null;
  protected ?WidgetGrid $widgetGrid = null;
  protected ?string $code = null;
  protected ?string $wording = null;
  protected ?string $comment = null;
  protected ?int $typeWidget = null;
  protected ?int $subTypeWidget = null;
  protected ?string $query = null;
  protected ?float $width = null;
  protected ?float $height = null;
  protected ?float $positionX = null;
  protected ?float $positionY = null;
  protected ?string $fontColor = null;
  protected ?string $backgroundColor = null;

  protected array $queryParameters;

  public function __construct()
  {

  }

  /**
   * @return int|null
   */
  public function getId(): ?int
  {
    return $this->id;
  }

  /**
   * @param int|null $id
   */
  public function setId(?int $id): void
  {
    $this->id = $id;
  }

  /**
   * @return WidgetGrid|null
   */
  public function getWidgetGrid(): ?WidgetGrid
  {
    return $this->widgetGrid;
  }

  /**
   * @param WidgetGrid|null $widgetGrid
   */
  public function setWidgetGrid(?WidgetGrid $widgetGrid): void
  {
    $this->widgetGrid = $widgetGrid;
  }

  /**
   * @return string|null
   */
  public function getCode(): ?string
  {
    return $this->code;
  }

  /**
   * @param string|null $code
   */
  public function setCode(?string $code): void
  {
    $this->code = $code;
  }

  /**
   * @return string|null
   */
  public function getWording(): ?string
  {
    return $this->wording;
  }

  /**
   * @param string|null $wording
   */
  public function setWording(?string $wording): void
  {
    $this->wording = $wording;
  }

  /**
   * @return string|null
   */
  public function getComment(): ?string
  {
    return $this->comment;
  }

  /**
   * @param string|null $comment
   */
  public function setComment(?string $comment): void
  {
    $this->comment = $comment;
  }

  /**
   * @return int|null
   */
  public function getTypeWidget(): ?int
  {
    return $this->typeWidget;
  }

  /**
   * @param int|null $typeWidget
   */
  public function setTypeWidget(?int $typeWidget): void
  {
    $this->typeWidget = $typeWidget;
  }

  /**
   * @return int|null
   */
  public function getSubTypeWidget(): ?int
  {
    return $this->subTypeWidget;
  }

  /**
   * @param int|null $subTypeWidget
   */
  public function setSubTypeWidget(?int $subTypeWidget): void
  {
    $this->subTypeWidget = $subTypeWidget;
  }

  /**
   * @return string|null
   */
  public function getQuery(): ?string
  {
    return $this->query;
  }

  /**
   * @param string|null $query
   */
  public function setQuery(?string $query): void
  {
    $this->query = $query;
  }

  /**
   * @return float|null
   */
  public function getWidth(): ?float
  {
    return $this->width;
  }

  /**
   * @param float|null $width
   */
  public function setWidth(?float $width): void
  {
    $this->width = $width;
  }

  /**
   * @return float|null
   */
  public function getHeight(): ?float
  {
    return $this->height;
  }

  /**
   * @param float|null $height
   */
  public function setHeight(?float $height): void
  {
    $this->height = $height;
  }

  /**
   * @return float|null
   */
  public function getPositionX(): ?float
  {
    return $this->positionX;
  }

  /**
   * @param float|null $positionX
   */
  public function setPositionX(?float $positionX): void
  {
    $this->positionX = $positionX;
  }

  /**
   * @return float|null
   */
  public function getPositionY(): ?float
  {
    return $this->positionY;
  }

  /**
   * @param float|null $positionY
   */
  public function setPositionY(?float $positionY): void
  {
    $this->positionY = $positionY;
  }

  /**
   * @return string|null
   */
  public function getFontColor(): ?string
  {
    return $this->fontColor;
  }

  /**
   * @param string|null $fontColor
   */
  public function setFontColor(?string $fontColor): void
  {
    $this->fontColor = $fontColor;
  }

  /**
   * @return string|null
   */
  public function getBackgroundColor(): ?string
  {
    return $this->backgroundColor;
  }

  /**
   * @param string|null $backgroundColor
   */
  public function setBackgroundColor(?string $backgroundColor): void
  {
    $this->backgroundColor = $backgroundColor;
  }

  /**
   * @return array|null
   */
  public function getQueryParameters(): ?array
  {
    return $this->queryParameters;
  }

  /**
   * @param array|null $queryParameters
   */
  public function setQueryParameters(?array $queryParameters): void
  {
    $this->queryParameters = $queryParameters;
  }

}