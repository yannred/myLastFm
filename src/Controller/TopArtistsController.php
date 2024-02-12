<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Import;
use App\Entity\Scrobble;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TopArtistsController extends AbstractController
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

    #[Route('/top/artists', name: 'app_top_artists')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
      $artistRepository = $this->entityManager->getRepository(Artist::class);

      $query = $artistRepository->createQueryBuilder('a')->orderBy('s.id', 'ASC')->getQuery();

      $artistPagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        self::LIMIT_PER_PAGE
      );

      return $this->render('last_scrobbles/index.html.twig', [
        'artists' => $artistPagination,
      ]);
    }
}
