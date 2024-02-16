<?php

namespace App\Controller;

use App\Entity\Widget;
use App\Entity\WidgetGrid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WidgetController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/myPage/widget/save', name: 'app_widget', methods: ['POST'])]
  public function saveWidget(Request $request): Response
  {

    //TODO : add error control

    $response = new Response();

    $body = $request->getContent();
    $parameters = json_decode($body, true);

//    dd($parameters);

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
