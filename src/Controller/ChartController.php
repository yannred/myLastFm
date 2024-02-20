<?php

namespace App\Controller;

use App\Data\chartItem;
use App\Entity\Widget;
use App\Entity\WidgetGrid;
use App\Service\StatisticsService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChartController extends AbstractController
{

  protected EntityManagerInterface $entityManager;
  protected LoggerInterface $logger;
  protected Security $security;
  protected StatisticsService $statisticsService;

  protected WidgetGrid $userWidgetGrid;

  public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, Security $security, StatisticsService $statisticsService)
  {
    $this->entityManager = $entityManager;
    $this->logger = $logger;
    $this->security = $security;
    $this->statisticsService = $statisticsService;

    //create a new grid if no grid is found
    $grid = $this->entityManager->getRepository(WidgetGrid::class)->findOneBy(['user' => $this->security->getUser(), 'defaultGrid' => true]);
    if ($grid === null) {
      $this->createGrid();
    } else {
      $this->userWidgetGrid = $grid;
    }
  }

  #[Route('/myPage/chart/{id}', name: 'app_chart', methods: ['GET'])]
  public function getChart(Request $request): Response
  {
    $response = new Response();
    $chart = new chartItem();

    $widget = $this->entityManager->getRepository(Widget::class)->getWidgetFromUser($request->get('id'), $this->userWidgetGrid->getUser()->getId());

    if ($widget === null) {
      $response->setStatusCode(Response::HTTP_NOT_FOUND);
    } else {
      $chart->id = $widget->getId();
      $chart->type = $widget->getChartType();
      $chart->label = $widget->getWording();
      $chart->data = $this->statisticsService->getDataForChart($widget);

      $response->setStatusCode(Response::HTTP_OK);
      $response->setContent(json_encode($chart));
    }

    return $response;
  }


  private function createGrid()
  {
    $grid = new WidgetGrid();
    $grid->setUser($this->security->getUser());
    $grid->setDefaultGrid(true);
    $grid->setCode('default');
    $grid->setWording('Created by default (' . date('Y-m-d H:i:s') . ')');

    $this->entityManager->persist($grid);
    $this->entityManager->flush();

    $this->userWidgetGrid = $grid;
  }

}
