<?php

namespace App\Data\Widget;

use App\Entity\Widget;

class TopArtistModel extends TopModel
{

  public function __construct()
  {

    parent::__construct();

    $this->subTypeWidget = Widget::SUB_TYPE__TOP_ARTIST;

    $this->setQueryParameters(
      [
        'entity' => 'App\Entity\Artist',
        'entityAlias' => 'artist',
        'select' => 'artist.name, count(scrobble.id) as count',
        'join' => [
          'artist.tracks' => 'track',
          'track.scrobbles' => 'scrobble',
        ],
        'groupby' => 'artist.name',
        'orderby' => [
          'count(scrobble.id)' => 'DESC'
        ]
      ]
    );
  }

}