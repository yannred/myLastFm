<?php

namespace App\Controller;

use App\Data\gridstackItem;
use App\Data\Widget\TopArtistModel;
use App\Entity\Widget;
use App\Entity\WidgetGrid;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WidgetController extends AbstractController
{

  protected EntityManagerInterface $entityManager;
  protected LoggerInterface $logger;
  protected Security $security;

  protected WidgetGrid $userWidgetGrid;

  public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger, Security $security)
  {
    $this->entityManager = $entityManager;
    $this->logger = $logger;
    $this->security = $security;

    //create a new grid if no grid is found
    $grid = $this->entityManager->getRepository(WidgetGrid::class)->findOneBy(['user' => $this->security->getUser(), 'defaultGrid' => true]);
    if ($grid === null) {
      $this->createGrid();
    } else {
      $this->userWidgetGrid = $grid;
    }
  }

  #[Route('/myPage/grid', name: 'app_widget_load_grid', methods: ['GET'])]
  public function loadGrid(): Response
  {
    $response = new Response();

    $gridStackItems = [];

    $widgetEntities = $this->userWidgetGrid->getWidgets();

    foreach ($widgetEntities as $widgetEntity) {
      $gridStackWidget = new gridstackItem($widgetEntity);

      $gridStackWidget->content = $this->generateContent($widgetEntity) . ' <br />' . $widgetEntity->getDeleteButton();

      $gridStackItems[] = $gridStackWidget;
    }

    $response->setStatusCode(Response::HTTP_OK);
    $response->setContent(json_encode($gridStackItems));

    return $response;
  }


  #[Route('/myPage/widget/new', name: 'app_widget_new', methods: ['GET'])]
  public function createWidget(): Response
  {
    $response = new Response();
    $gridRepository = $this->entityManager->getRepository(WidgetGrid::class);
    $widgetRepository = $this->entityManager->getRepository(Widget::class);

    $typeWidget = Widget::TYPE__QUERY;
    $subTypeWidget = Widget::SUB_TYPE__TOP_ARTIST;

    $model = null;
    switch ($typeWidget) {

      case Widget::TYPE__QUERY:{

        /** ********** */
        /** QUERY TYPE */
        /** ********** */
        switch ($subTypeWidget) {

          /** QUERY TOP ARTIST */
          case Widget::SUB_TYPE__TOP_ARTIST:{
            $model = new TopArtistModel();
            break;
          }
        }
        break;
      }

      /** ********** */
      /**            */
      /** ********** */
      case 'else':{

        break;
      }
    }

    $widget = new Widget();
    $widget->createFrom($model);
    $widget->setQuery($widgetRepository->createWidgetQuery($model->getQueryParameters(), $this->security->getUser())->getDQL());

    $widget->setCode(' (' . date('Y-m-d H:i:s').') ');
    $widget->setTypeWidget(Widget::TYPE__QUERY);
    $widget->setWidgetGrid($this->userWidgetGrid);

    $widget->setWidth(2);
    $widget->setHeight(1);
    $widget->setPositionX(0);
    $widget->setPositionY($gridRepository->getNextPositionY($this->userWidgetGrid));


    $this->entityManager->persist($widget);
    $this->entityManager->flush();

    $gridstackItem = new gridstackItem($widget);
    $gridstackItem->content = $this->generateContent($widget) . ' <br />' . $widget->getDeleteButton();

    $response->setStatusCode(Response::HTTP_CREATED);
    $response->setContent(json_encode($gridstackItem));

    return $response;
  }



  #[Route('/myPage/widget/{id}', name: 'app_widget_update', methods: ['UPDATE'])]
  public function updateWidget(Request $request, ): Response
  {
    $response = new Response();

    $body = $request->getContent();
    $parameters = json_decode($body, true);

    $widget = $this->entityManager->getRepository(Widget::class)->findOneBy(['id' => $parameters['id'], 'widgetGrid' => $this->userWidgetGrid]);

    if ($widget === null) {
      $response->setStatusCode(Response::HTTP_NOT_FOUND);
    } else {
      $widget->setWidth($parameters['w']);
      $widget->setHeight($parameters['h']);
      $widget->setPositionX($parameters['x']);
      $widget->setPositionY($parameters['y']);

      $this->entityManager->persist($widget);
      $this->entityManager->flush();

      $response->setStatusCode(Response::HTTP_CREATED);
      $response->setContent(json_encode(['id' => $widget->getId()]));
    }

    return $response;
  }


  #[Route('/myPage/widget/{id}', name: 'app_widget_delete', methods: ['DELETE'])]
  public function deleteWidget(Request $request, ): Response
  {
    $response = new Response();

    $widget = $this->entityManager->getRepository(Widget::class)->findOneBy(['id' => $request->get('id'), 'widgetGrid' => $this->userWidgetGrid]);

    if ($widget === null) {
      $response->setStatusCode(Response::HTTP_NOT_FOUND);
    } else {
      $this->entityManager->remove($widget);
      $this->entityManager->flush();
      $response->setStatusCode(Response::HTTP_OK);
    }

    return $response;
  }


  private function createGrid()
  {
    $grid = new WidgetGrid();
    $grid->setUser($this->security->getUser());
    $grid->setDefaultGrid(true);
    $grid->setCode('default');
    $grid->setWording('Created by default (' . date('Y-m-d H:i:s').')');

    $this->entityManager->persist($grid);
    $this->entityManager->flush();

    $this->userWidgetGrid = $grid;
  }


  private function generateContent($widget): ?string
  {

    $results = $this->entityManager->createQuery($widget->getQuery())->getResult();
    $results = array_slice($results, 0, 3);
    $content = 'Top Artist : <br />';
    foreach ($results as $result) {
      $content .= $result['name'] . " " . $result['count'] . 'x<br />';
    }
    return $content;
  }

}
