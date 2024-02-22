<?php

namespace App\Data\Widget;

use App\Entity\Widget;

class TopArtistsModel extends TopModel
{

  public function __construct()
  {

    parent::__construct();

    $this->setCode('TOPARTIST - ' . date('Y-m-d H:i:s'));
    $this->setTypeWidget(Widget::TYPE__TOP_ARTISTS);
    $this->setWidth(4);
    $this->setHeight(4);

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