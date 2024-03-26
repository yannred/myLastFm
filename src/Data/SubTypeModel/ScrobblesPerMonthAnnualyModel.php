<?php

namespace App\Data\SubTypeModel;
class ScrobblesPerMonthAnnualyModel extends AbstractSubTypeModel
{
  public function __construct(){
    parent::__construct();

    $this->chartType = 'bar';

    $this->chartOptions->legendVisible = false;
    $this->chartOptions->aspectRatio = 2;
    $this->chartOptions->ticksVisibleY = false;
  }
}