<?php

namespace App\Data\SubTypeModel;
class BarModel extends AbstractSubTypeModel
{
  public function __construct(){
    parent::__construct();

    $this->chartType = 'bar';

    $this->chartOptions->legendVisible = false;
  }
}