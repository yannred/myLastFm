<?php

namespace App\Data;

class SearchBarData
{
  public string $type;

  public ?\DateTimeInterface $from;
  public ?\DateTimeInterface $to;

  public ?string $trackName;
  public ?string $artistName;
  public ?string $albumName;

  public function __construct()
  {
    $this->type = '';
    $this->from = null;
    $this->to = null;
    $this->trackName = '';
    $this->artistName = '';
    $this->albumName = '';
  }
}