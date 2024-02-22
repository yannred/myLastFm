<?php

namespace App\Data\Widget;

use App\Entity\Widget;

class TopAlbumsModel extends TopModel
{

  public function __construct()
  {

    parent::__construct();

    $this->setCode('TOPALBUM - ' . date('Y-m-d H:i:s'));
    $this->setTypeWidget(Widget::TYPE__TOP_ARTISTS);
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
      'entity' => 'App\Entity\Album',
      'entityAlias' => 'album',
      'select' => 'album.name, count(scrobble.id) as count',
      'join' => [
        'album.tracks' => 'track',
        'track.scrobbles' => 'scrobble',
      ],
      'groupby' => 'album.name',
      'orderby' => [
        'count(scrobble.id)' => 'DESC'
      ]
    ];

    if ($widget->getDateTo() !== null) {
      $parameters['where'] = [
        'and' => ['value' => 'scrobble.timestamp > ' . $widget->getDateFrom()->getTimestamp()
          . ' AND scrobble.timestamp < ' . $widget->getDateTo()->getTimestamp()]
      ];
    }

    return $parameters;
  }



}