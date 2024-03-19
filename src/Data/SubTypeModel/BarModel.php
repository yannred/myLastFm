<?php

namespace App\Data\SubTypeModel;

use App\Data\ChartOptions;

class BarModel extends AbstractSubTypeModel
{
  public function __construct(
    public ChartOptions $chartOptions = new ChartOptions(),
  ){
    parent::__construct($chartOptions);
    $this->chartOptions->legendVisible = false;
  }
}