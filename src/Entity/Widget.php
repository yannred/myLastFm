<?php

namespace App\Entity;

use App\Data\Widget\TopAlbumsModel;
use App\Data\Widget\TopArtistsModel;
use App\Data\Widget\TopTracksModel;
use App\Data\Widget\WidgetModel;
use App\Repository\WidgetRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WidgetRepository::class)]
class Widget
{

  const TYPE__TOP_ARTISTS = 1;
  const TYPE__TOP_ALBUMS = 2;
  const TYPE__TOP_TRACKS = 3;
  const TYPES = [
    'Top Artists' => self::TYPE__TOP_ARTISTS,
    'Top Albums' => self::TYPE__TOP_ALBUMS,
    'Top Tracks' => self::TYPE__TOP_TRACKS
  ];

  const SUB_TYPE__BAR = 1;
  const SUB_TYPE__PIE = 2;
  const SUB_TYPE__DONUT = 3;
  const SUB_TYPES = [
    'Bar' => self::SUB_TYPE__BAR,
    'Pie' => self::SUB_TYPE__PIE,
    'Donut' => self::SUB_TYPE__DONUT
  ];

//  const WIDGET_DEFAULT_FONT_COLOR = '#ffffff';
  const WIDGET_DEFAULT_FONT_COLOR = 'black';
//  const WIDGET_DEFAULT_BACKGROUND_COLOR = '#d7d7f';
  const WIDGET_DEFAULT_BACKGROUND_COLOR = '#eaeaea';


  const DATE_TYPE__ALL_TIME = 0;
  const DATE_TYPE__CUSTOM = 1;
  const DATE_TYPE__LAST_WEEK = 2;
  const DATE_TYPE__LAST_MONTH = 3;
  const DATE_TYPE__LAST_3_MONTHS = 4;
  const DATE_TYPE__LAST_6_MONTHS = 5;
  const DATE_TYPE__LAST_YEAR = 6;
  const DATE_TYPES = [
    'All Time' => self::DATE_TYPE__ALL_TIME,
    'Custom period' => self::DATE_TYPE__CUSTOM,
    'Last Week' => self::DATE_TYPE__LAST_WEEK,
    'Last Month' => self::DATE_TYPE__LAST_MONTH,
    'Last 3 Months' => self::DATE_TYPE__LAST_3_MONTHS,
    'Last 6 Months' => self::DATE_TYPE__LAST_6_MONTHS,
    'Last Year' => self::DATE_TYPE__LAST_YEAR
  ];

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

  #[ORM\Column]
  private ?int $dateType = null;

  #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $dateFrom = null;

  #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
  private ?\DateTimeInterface $dateTo = null;




  /**
   * Validate date range :
   * add 1 day to the end date if it's the same as the start date
   * check if the start date is before the end date
   * @return bool
   */
  public function validateDateRange(): bool
  {
    if ($this->getDateFrom() == $this->getDateTo()) {
      //add 1 day
      $this->setDateTo($this->getDateTo()->modify('+1 day'));
    }

    if ($this->getDateFrom() > $this->getDateTo()) {
      return false;
    }

    return true;
  }




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

  public function applyModel(mixed $widgetModel): void
  {
    $this->setCode($widgetModel->getCode());
    $this->setWidth($widgetModel->getWidth());
    $this->setHeight($widgetModel->getHeight());
    $this->setTypeWidget($widgetModel->getTypeWidget());
  }

  public function getDeleteButton($class = 'delete-widget'): string
  {
    $javascriptFunction = 'deleteWidget("' . $this->getId() . '")';
    return '<button onclick=' . $javascriptFunction . ' class="'.$class.'">X</button>';
  }

  public static function getWidgetModelFromType(int $typeWidget): ?WidgetModel
  {
    $model = null;
    switch ($typeWidget) {

      /** ********************* */
      /**    TOP ARTIST TYPE    */
      /** ********************* */
      case Widget::TYPE__TOP_ARTISTS:

        $model = new TopArtistsModel();
        break;

      /** ********************* */
      /**    TOP ARTIST TYPE    */
      /** ********************* */
      case Widget::TYPE__TOP_ALBUMS:

        $model = new TopAlbumsModel();
        break;

      /** ********************* */
      /**    TOP TRACKS TYPE    */
      /** ********************* */
      case Widget::TYPE__TOP_TRACKS:

        $model = new TopTracksModel();
        break;

    }

    return $model;
  }

  public function getWidgetModel(): ?WidgetModel
  {
    return self::getWidgetModelFromType($this->typeWidget, $this->subTypeWidget);
  }


  public static function getChartTypeFromSubType(int $subTypeWidget): string
  {
    $chartType = '';

    switch ($subTypeWidget) {
      case Widget::SUB_TYPE__BAR:
        $chartType = 'bar';
        break;

      case Widget::SUB_TYPE__PIE:
        $chartType = 'pie';
        break;

      case Widget::SUB_TYPE__DONUT:
        $chartType = 'doughnut';
        break;
    }

    return $chartType;
  }

  public function getChartType(): string
  {
    return self::getChartTypeFromSubType($this->subTypeWidget);
  }

  public function getDateType(): ?int
  {
      return $this->dateType;
  }

  public function setDateType(int $dateType): static
  {
      $this->dateType = $dateType;

      return $this;
  }

  public function getDateFrom(): ?\DateTimeInterface
  {
      return $this->dateFrom;
  }

  public function setDateFrom(?\DateTimeInterface $dateFrom): static
  {
      $this->dateFrom = $dateFrom;

      return $this;
  }

  public function getDateTo(): ?\DateTimeInterface
  {
      return $this->dateTo;
  }

  public function setDateTo(?\DateTimeInterface $dateTo): static
  {
      $this->dateTo = $dateTo;

      return $this;
  }



}

