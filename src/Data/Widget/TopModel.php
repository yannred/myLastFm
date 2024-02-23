<?php

namespace App\Data\Widget;

use App\Entity\Widget;

abstract class TopModel extends WidgetModel
{
  public function __construct()
  {
    parent::__construct();
  }

  /**
   * Return the parameters for create the DQL query
   * @param Widget $widget
   * @return array
   */
  public function getQueryParameters(Widget $widget): array
  {
    $parameters = [];

    if ($widget->getDateType() == Widget::DATE_TYPE__LAST_WEEK) {
      $parameters['where'] = [
        'and' => ['value' => 'scrobble.TIMESTAMP >  UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 WEEK)) '
          . ' AND scrobble.timestamp < UNIX_TIMESTAMP(CURDATE()']
      ];
    }
    if ($widget->getDateType() == Widget::DATE_TYPE__LAST_MONTH) {
      $parameters['where'] = [
        'and' => ['value' => 'scrobble.TIMESTAMP >  UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) '
          . ' AND scrobble.timestamp < UNIX_TIMESTAMP(CURDATE()']
      ];
    }
    if ($widget->getDateType() == Widget::DATE_TYPE__LAST_3_MONTHS) {
      $parameters['where'] = [
        'and' => ['value' => 'scrobble.TIMESTAMP >  UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 3 MONTH)) '
          . ' AND scrobble.timestamp < UNIX_TIMESTAMP(CURDATE()']
      ];
    }
    if ($widget->getDateType() == Widget::DATE_TYPE__LAST_6_MONTHS) {
      $parameters['where'] = [
        'and' => ['value' => 'scrobble.TIMESTAMP >  UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 6 MONTH)) '
          . ' AND scrobble.timestamp < UNIX_TIMESTAMP(CURDATE()']
      ];
    }
    if ($widget->getDateType() == Widget::DATE_TYPE__LAST_YEAR) {
      $parameters['where'] = [
        'and' => ['value' => 'scrobble.TIMESTAMP >  UNIX_TIMESTAMP(DATE_SUB(CURDATE(), INTERVAL 1 YEAR)) '
          . ' AND scrobble.timestamp < UNIX_TIMESTAMP(CURDATE()']
      ];
    }

    return $parameters;
  }


}