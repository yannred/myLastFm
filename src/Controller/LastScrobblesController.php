<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LastScrobblesController extends AbstractController
{
  #[Route('/myAccount/lastScrobbles', name: 'app_last_scrobbles')]
  public function index(): Response
  {
    return $this->render('last_scrobbles/index.html.twig', [
      'controller_name' => 'LastScrobblesController',
    ]);
  }
}
