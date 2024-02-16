<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyStatisticsController extends AbstractController
{
  #[Route('/myPage/myStatistics', name: 'app_my_statistics')]
  public function index(): Response
  {
    return $this->render('my_statistics/index.html.twig', [
      'controller_name' => 'MyStatisticsController',
    ]);
  }

}
