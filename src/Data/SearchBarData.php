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

  public $groupBy;

  const GROUP_BY_NONE = 0;
  const GROUP_BY_ARTIST = 1;
  const GROUP_BY_ALBUM = 2;
}