<?php

namespace App\Data;

class SearchBarData
{
  public string $type;

  public ?\DateTimeInterface $from;
  public ?\DateTimeInterface $to;

  public ?string $track;
  public ?string $artist;
  public ?string $album;

  public function __construct()
  {
    $this->type = '';
    $this->from = null;
    $this->to = null;
    $this->track = '';
    $this->artist = '';
    $this->album = '';
  }
}