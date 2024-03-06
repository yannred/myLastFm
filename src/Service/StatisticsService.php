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

        $spinner = '
          <div class="text-center grow flex items-center justify-center">
            <div role="status">
              <svg aria-hidden="true" class="inline w-16 h-16 text-gray-200 animate-spin dark:text-gray-600 fill-blue-600" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
                <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z" fill="currentFill"/>
              </svg>
              <span class="sr-only">Loading...</span>
            </div>
          </div>
        ';

        $subContent = '<canvas id="canvas-' . $widget->getId() . '" class="widget-canvas"></canvas>';
        $content = $widget->getDeleteButton() . $widget->getModifyButton() . $widget->getInfoButton() . '
          <div id="widget-chart-' . $widget->getId() . '" class="widget-chart flex flex-col vstack m-0" style="background-color: ' . $widget->getBackgroundColor() . '; color: ' . $widget->getFontColor() . ';">
            <div class="flex-non">' . $widget->getWording() . '</div>' .
            $spinner .
            '<div class="col p-2" style="height: 0;">' . $subContent . '</div>
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

        $query = $widget->getQuery();
        $allResults = $this->em->getConnection()->executeQuery($query)->fetchAllAssociative();

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

        $query = $widget->getQuery();
        $allResults = $this->em->getConnection()->executeQuery($query)->fetchAllAssociative();

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


  /**
   * Create the SQL query from the parameters
   * TODO : create DQL datetime functions for use DQL instead of SQL
   * @param array $parameters
   * @return string
   */
  public function createSqlQuery(array $parameters): string
  {
    $query = $parameters['select'] . " " . $parameters['from'] . " ";

    if (isset($parameters['join'])) {
      $query .= $parameters['join'] . " ";
    }

    $query .= $parameters['where'] . " ";

    if (isset($parameters['groupby'])) {
      $query .= $parameters['groupby'] . " ";
    }
    if (isset($parameters['orderby'])) {
      $query .= $parameters['orderby'] . " ";
    }

    return $query;
  }

}