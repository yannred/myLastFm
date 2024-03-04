<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
  public function getFilters(): array
  {
    return [
      new TwigFilter('youtubeSearchLink', [$this, 'createYoutubeSearchLink']),
    ];
  }

  public function createYoutubeSearchLink(string $track, string $artist): string
  {
    $artist = urlencode($artist);
    $track = urlencode($track);
    $search = $artist . ' ' . $track;
    $search = str_replace(' ', '+', $search);
    $link = 'https://www.youtube.com/results?search_query=' . $search;

    return $link;
  }
}