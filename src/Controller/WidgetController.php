<?php

namespace App\Controller;

use App\Data\gridstackItem;
use App\Data\Notification;
use App\Entity\Widget;
use App\Entity\WidgetGrid;
use App\Form\WidgetType;
use App\Service\StatisticsService;
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

  #[Route('/myPage/grid', name: 'app_widget_load_grid', methods: ['GET'])]
  public function loadGrid(): Response
  {
    $response = new Response();

    $gridStackItems = [];

    $widgetEntities = $this->userWidgetGrid->getWidgets();

    foreach ($widgetEntities as $widgetEntity) {
      $gridStackWidget = new gridstackItem($widgetEntity);
      $modelWidget = $widgetEntity->getWidgetModel();

      $gridStackWidget->content = $this->statisticsService->generateContent($widgetEntity, $modelWidget->getContentParameters());

      $gridStackItems[] = $gridStackWidget;
    }

    $response->setStatusCode(Response::HTTP_OK);
    $response->setContent(json_encode($gridStackItems));

    return $response;
  }


  #[Route('/myPage/widget', name: 'app_widget_new', methods: ['POST'])]
  public function createWidgetProto(): Response
  {
    $response = new Response();
    $gridRepository = $this->entityManager->getRepository(WidgetGrid::class);
    $widgetRepository = $this->entityManager->getRepository(Widget::class);

    $typeWidget = Widget::TYPE__TOP_ARTIST;
    $subTypeWidget = Widget::SUB_TYPE__BAR;

    $model = Widget::getWidgetModelFromType($typeWidget);

    $widget = new Widget();
    $widget->applyModel($model);
    $widget->setPositionX(0);
    $widget->setPositionY($gridRepository->getNextPositionY($this->userWidgetGrid));

    $widget->setSubTypeWidget($subTypeWidget);
    $widget->setWording('New widget');

    $widget->setQuery(
      $widgetRepository
      ->createWidgetQuery($model->getQueryParameters())
      ->getDQL()
    );

    $widget->setWidgetGrid($this->userWidgetGrid);

    $this->entityManager->persist($widget);
    $this->entityManager->flush();

    $gridstackItem = new gridstackItem($widget);
    $gridstackItem->content = $this->statisticsService->generateContent($widget, $model->getContentParameters());

    $response->setStatusCode(Response::HTTP_CREATED);
    $response->setContent(json_encode($gridstackItem));

    return $response;
  }

  #[Route('/myPage/myStatistics/new', name: 'app_widget_new_statistic')]
  public function newStatistic(Request $request): Response
  {
    $response = new Response();
    $view = 'my_statistics/new.html.twig';
    $notifications = [];

    $gridRepository = $this->entityManager->getRepository(WidgetGrid::class);
    $widgetRepository = $this->entityManager->getRepository(Widget::class);


    $form = $this->createForm(WidgetType::class);
    $form->handleRequest($request);
    $paramView = ['form' => $form];

    if ($form->isSubmitted() && $form->isValid()) {

      try {
        $success = true;

        /** @var Widget $widget */
        $widget = $form->getData();

        //controls
        if ($widget->getTypeWidget() == 0) {
          $notifications[] = new Notification('Statistic type is required', 'warning');
          $success = false;
        }

        if ($widget->getSubTypeWidget() == 0) {
          $notifications[] = new Notification('Chart type is required', 'warning');
          $success = false;
        }

        if ($widget->getFontColor() == '') {
          $notifications[] = new Notification('Font color is required', 'warning');
          $success = false;
        }
        if ($widget->getBackgroundColor() == '') {
          $notifications[] = new Notification('Background color is required', 'warning');
          $success = false;
        }

        if ($success) {

          //All Controls OK
          $model = $widget->getWidgetModel();
          $widget->applyModel($model);
          $widget->setPositionX(0);
          $widget->setPositionY($gridRepository->getNextPositionY($this->userWidgetGrid));

          $widget->setQuery(
            $widgetRepository
              ->createWidgetQuery($model->getQueryParameters())
              ->getDQL()
          );

          $widget->setWidgetGrid($this->userWidgetGrid);

          $this->entityManager->persist($widget);
          $this->entityManager->flush();

          $notifications[] = new Notification('Statistic created', 'success');
          $response->setStatusCode(Response::HTTP_CREATED);
          $view = 'my_statistics/index.html.twig';

        } else {

          //Error during controls
          $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

      } catch (\Exception $e) {
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $notifications[] = new Notification('Internal error : ' . $e->getMessage(), 'danger');
      }
    }

    $paramView['notifications'] = $notifications;
    return $this->render($view, $paramView, $response);
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

  //TODO : move in WidgetGrid Entity
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

}
