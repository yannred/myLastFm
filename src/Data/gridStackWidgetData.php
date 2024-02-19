<?php

namespace App\Data;

class gridStackWidgetData
{
  public int $id;
  public string $content;

  public function __construct()
  {
    $this->content = '';
  }
}