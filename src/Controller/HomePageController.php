<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Service\ApiRequest;
use App\Service\EntityService\EntityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{
  #[Route('/', name: 'app_home_page')]
  public function index(EntityService $artistService): Response
  {


    return $this->render('home_page/index.html.twig', [
      'perdu' => 'perdu',
      'test' => '$string',
    ]);
  }
}