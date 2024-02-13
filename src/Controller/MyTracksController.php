<?php

namespace App\Controller;

use App\Entity\Track;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyTracksController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  //TODO : move exception constants
  const EXCEPTION_ERROR = 0;
  const EXCEPTION_NO_DATA = 1;

  const LIMIT_PER_PAGE = 20;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  //TODO : re use the ScrobblerController::updateScrobble() method

  #[Route('/myPage/myTracks', name: 'app_my_tracks')]
  public function index(Request $request, PaginatorInterface $paginator): Response
  {
    $trackRepository = $this->entityManager->getRepository(Track::class);

    $query = $trackRepository->createQueryBuilder('t')->getQuery();

    $tracksPagination = $paginator->paginate(
      $query,
      $request->query->getInt('page', 1),
      self::LIMIT_PER_PAGE
    );

    return $this->render('my_tracks/index.html.twig', [
      'tracks' => $tracksPagination,
      'pagination' => "1"
    ]);
  }
}
