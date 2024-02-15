<?php

namespace App\Data;

class SearchBarData
{
  public string $type;

  public \DateTimeInterface $from;
  public \DateTimeInterface $to;

  public string $trackName;
  public string $artistName;
  public string $albumName;
}