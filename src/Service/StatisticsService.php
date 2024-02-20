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
    $results = $this->em->createQuery($widget->getQuery())->getResult();
    $results = array_slice($results, 0, 3);
    $subContent = 'Top Artist : <br />';
    foreach ($results as $result) {
      $subContent .= $result['name'] . " " . $result['count'] . 'x<br />';
    }

    $content = '<p>' . $widget->getDeleteButton() . '</p>';
    $content .= '<div id="widget-chart-' . $widget->getId() . '" class="widget-chart"> ' . $subContent . ' </div>';

    if ($widget->getId() == 113){
      $subContent = $widget->getDeleteButton() . '<canvas id="myChart"></canvas>';
      $content = '<div id="widget-chart-' . $widget->getId() . '" class="widget-chart"> ' . $subContent . ' </div>';
    }

    return $content;
  }

}