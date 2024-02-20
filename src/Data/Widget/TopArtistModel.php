<?php

namespace App\Data\Widget;

use App\Entity\Widget;

class TopArtistModel extends TopModel
{

  public function __construct($subTypeWidget = Widget::SUB_TYPE__BAR)
  {

    parent::__construct();

    $this->typeWidget = Widget::TYPE__TOP_ARTIST;
    $this->subTypeWidget = $subTypeWidget;

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