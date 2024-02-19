<?php

namespace App\Controller;

use App\Data\gridStackWidgetData;
use App\Entity\Widget;
use App\Entity\WidgetGrid;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WidgetController extends AbstractController
{

  protected EntityManagerInterface $entityManager;
  protected LoggerInterface $logger;

  public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
  {
    $this->entityManager = $entityManager;
    $this->logger = $logger;
  }

  #[Route('/myPage/widget/load/grid', name: 'app_widget_load_grid', methods: ['GET'])]
  public function loadGrid(Request $request): Response
  {
    $response = new Response();

    $gridStackWidgets = [];

    $widgetRepository = $this->entityManager->getRepository(Widget::class);
    $widgetEntities = $widgetRepository->findBy(['widgetGrid' => 1]);

    foreach ($widgetEntities as $widgetEntity) {
      $gridStackWidget = new gridStackWidgetData();
      $gridStackWidget->id = $widgetEntity->getId();
      $gridStackWidget->content = $widgetEntity->getcode();
      $gridStackWidgets[] = $gridStackWidget;
    }

    $response->setStatusCode(Response::HTTP_OK);
    $response->setContent(json_encode($gridStackWidgets));

    return $response;
  }



  #[Route('/myPage/widget/save', name: 'app_widget_save', methods: ['POST'])]
  public function saveWidget(Request $request, ): Response
  {

    //TODO : add error control

    $response = new Response();

    $body = $request->getContent();
    $parameters = json_decode($body, true);


    $this->logger->info('incoming data :');
    $this->logger->info(print_r($parameters, true));

    if ($parameters['id'] == 0){
      $widgetGrid = $this->entityManager->getRepository(WidgetGrid::class)->find(1);

      $widget = new Widget();
      $widget->setCode("widget_code");
      $widget->setTypeWidget(Widget::TYPE_WIDGET_QUERY);
      $widget->setWidgetGrid($widgetGrid);
    } else {
      $widget = $this->entityManager->getRepository(Widget::class)->find($parameters['id']);
    }

    $widget->setWidth($parameters['w']);
    $widget->setHeight($parameters['h']);
    $widget->setPositionX($parameters['x']);
    $widget->setPositionY($parameters['y']);


    $this->entityManager->persist($widget);
    $this->entityManager->flush();


    $response->setStatusCode(Response::HTTP_CREATED);
    $response->setContent(json_encode(['id' => $widget->getId()]));

    return $response;
  }

}
