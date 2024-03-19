<?php

namespace App\Data\SubTypeModel;

use App\Data\ChartOptions;

abstract class AbstractSubTypeModel
{
  public function __construct(
    private ChartOptions $chartOptions = new ChartOptions(),
  ){
  }

  public function getChartOptions(): ChartOptions
  {
    return $this->chartOptions;
  }
}