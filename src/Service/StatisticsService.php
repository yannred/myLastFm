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
      case Widget::SUB_TYPE__PIE :
      case Widget::SUB_TYPE__DONUT :
        $subContent = '<canvas id="canvas-' . $widget->getId() . '" class="widget-canva"></canvas>';
        $content = $widget->getDeleteButton() . $widget->getModifyButton() . '
          <div id="widget-chart-' . $widget->getId() . '" class="widget-chart vstack m-0" style="background-color: ' . $widget->getBackgroundColor() . '; color: ' . $widget->getFontColor() . ';">
            <div class="">' . $widget->getWording() . '</div>
            <div class="col p-2" style="height: 95%;">' . $subContent . '</div>
          </div>
        ';
        break;

    }

    return $content;
  }


  public function getDataForChart(Widget $widget): array
  {
    $data = [];

    switch ($widget->getSubTypeWidget()) {

      case Widget::SUB_TYPE__BAR :
        $allResults = $this->em->createQuery($widget->getQuery())->getResult();

        if (count($allResults) > 0) {
          $total = 0;
          foreach ($allResults as $result) {
            $total += $result['count'];
          }
          $total = ['name' => 'All', 'count' => $total];

          $firstResults = array_slice($allResults, 0, 5);

          $results = array_merge([$total], $firstResults);

          foreach ($results as $result) {
            $data[] = [
              'label' => $result['name'],
              'data' => $result['count']
            ];
          }
        }
        break;

      case Widget::SUB_TYPE__PIE :
      case Widget::SUB_TYPE__DONUT :

        $allResults = $this->em->createQuery($widget->getQuery())->getResult();

        if (count($allResults) > 0) {
          $firstResults = array_slice($allResults, 0, 5);

          $total = 0;
          foreach ($allResults as $result) {
            $total += $result['count'];
          }

          $totalFirstResults = 0;
          foreach ($firstResults as $result) {
            $totalFirstResults += $result['count'];
          }
          $others = ['name' => 'Others', 'count' => $total - $totalFirstResults];

          $results = array_merge([$others], $firstResults);

          foreach ($results as $result) {
            $data[] = [
              'label' => $result['name'],
              'data' => $result['count']
            ];
          }
        }

        break;
    }

    // dump($data);
    return $data;
  }

}