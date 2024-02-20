<?php

namespace App\Data;

class chartItem
{
  public int $id;
  public string $type;
  public string $label;
  public array $options;
  public array $data;

  public function __construct()
  {
    $this->id = 0;
    $this->type = '';
    $this->label = '';
    $this->options = [];
    $this->data = [];
  }
}