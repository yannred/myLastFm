<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyStatisticsActionPage extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/myPage/myStatistics/new/wiget', name: 'app_my_statistics_action_new')]
  public function index(): Response
  {
    $view = 'home_page/index.html.twig';
    return $this->render($view);
  }
}
