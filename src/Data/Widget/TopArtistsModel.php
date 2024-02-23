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
  }

  /**
   * Return the parameters for create the DQL query
   * @param Widget $widget
   * @return array
   */
  public function getQueryParameters(Widget $widget): array
  {
    $parameters = [
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
    ];

    if ($widget->getDateType() == Widget::DATE_TYPE__CUSTOM) {
      $parameters['where'] =
      [
        'and' => ['value' => 'scrobble.timestamp > ' . $widget->getDateFrom()->getTimestamp()
          . ' AND scrobble.timestamp < ' . $widget->getDateTo()->getTimestamp()]
      ];
    }

    return $parameters;
  }

}