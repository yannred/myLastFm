<?php

namespace App\Service;

use App\Entity\Widget;
use Doctrine\ORM\EntityManagerInterface;

class StatisticsService
{

  private EntityManagerInterface $em;

  public function __construct(EntityManagerInterface $em, ApiRequestService $apiRequestService)
  {
    $this->em = $em;
  }

  public function generateContent(?Widget $widget, array $parameters): string
  {
    $content = '';

    switch ($widget->getSubTypeWidget()) {

      case Widget::SUB_TYPE__BAR :
        $subContent = $widget->getDeleteButton() . '<canvas id="canvas-' . $widget->getId() . '" class="widget-canva"></canvas>';
        $content = '<div id="widget-chart-' . $widget->getId() . '" class="widget-chart"> ' . $subContent . ' </div>';

    }


    return $content;
  }


  public function getDataForChart (Widget $widget): array
  {
    $allResults = $this->em->createQuery($widget->getQuery())->getResult();

    $total = 0;
    foreach ($allResults as $result) {
      $total += $result['count'];
    }
    $total = ['name' => 'Total', 'count' => $total];

    $firstResults = array_slice($allResults, 0, 5);

    $results = array_merge([$total], $firstResults);

    $data = [];
    foreach ($results as $result) {
      $data[] = [
        'label' => $result['name'],
        'data' => $result['count']
      ];
    }
    return $data;
  }

}