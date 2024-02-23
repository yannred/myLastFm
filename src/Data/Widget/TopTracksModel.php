<?php

namespace App\Data\Widget;

use App\Entity\Widget;

class TopTracksModel extends TopModel
{

  public function __construct()
  {

    parent::__construct();

    $this->setCode('TOPALBUM - ' . date('Y-m-d H:i:s'));
    $this->setTypeWidget(Widget::TYPE__TOP_TRACKS);
    $this->setWidth(2);
    $this->setHeight(2);
  }


  /**
   * Return the parameters for create the DQL query
   * @param Widget $widget
   * @return array
   */
  public function getQueryParameters(Widget $widget): array
  {
    $parameters = [
      'entity' => 'App\Entity\Track',
      'entityAlias' => 'track',
      'select' => "CONCAT(track.name, '|', album.name, '|', artist.name) as name, count(scrobble.id) as count",
      'join' => [
        'track.album' => 'album',
        'album.artist' => 'artist',
        'track.scrobbles' => 'scrobble',
      ],
      'groupby' => 'track.name',
      'orderby' => [
        'count(scrobble.id)' => 'DESC'
      ]
    ];

    if ($widget->getDateType() == Widget::DATE_TYPE__CUSTOM) {
      $parameters['where'] = [
        'and' => ['value' => 'scrobble.timestamp > ' . $widget->getDateFrom()->getTimestamp()
          . ' AND scrobble.timestamp < ' . $widget->getDateTo()->getTimestamp()]
      ];
    }

    return $parameters;
  }

}