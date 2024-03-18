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
      new TwigFilter('truncateAndSuspension', [$this, 'truncateAndSuspension']),
    ];
  }

  /**
   * Create a youtube search link
   * @param string $track
   * @param string $artist
   * @return string
   */
  public function createYoutubeSearchLink(string $track, string $artist): string
  {
    $artist = urlencode($artist);
    $track = urlencode($track);
    $search = $artist . ' ' . $track;
    $search = str_replace(' ', '+', $search);
    $link = 'https://www.youtube.com/results?search_query=' . $search;

    return $link;
  }

  /**
   * Truncate a string and add suspension points if it's too long
   * @param string $string
   * @param int $lenght
   * @return string
   */
  public function truncateAndSuspension(string $string, int $lenght): string
  {
    if (strlen($string) > $lenght) {
      $string = substr($string, 0, $lenght);
      $string = $string . '...';
    }

    return $string;
  }
}