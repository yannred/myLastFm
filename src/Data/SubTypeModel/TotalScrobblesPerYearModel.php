<?php

namespace App\Data\SubTypeModel;

class TotalScrobblesPerYearModel extends AbstractSubTypeModel
{
  public function __construct(){
    parent::__construct();

    $this->chartType = 'bar';

    $this->chartOptions->legendVisible = false;
    $this->chartOptions->indexAxis = 'y';
    $this->chartOptions->aspectRatio = 2;
  }
}