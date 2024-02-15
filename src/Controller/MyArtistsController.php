<?php

namespace App\Controller;

use App\Data\SearchBarData;
use App\Entity\Artist;
use App\Form\SearchBarType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyArtistsController extends AbstractController
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

    #[Route('/myPage/myArtists', name: 'app_my_artists')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {

      $artistRepository = $this->entityManager->getRepository(Artist::class);

      $searchBarData = new SearchBarData();
      $queryForm = $this->createForm(SearchBarType::class, $searchBarData);
      $queryForm->handleRequest($request);

      $query = $artistRepository->paginationFilteredQuery($searchBarData);

      $artistPagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        self::LIMIT_PER_PAGE
      );

      return $this->render('my_artists/index.html.twig', [
        'artists' => $artistPagination,
        'pagination' => "1",
        'userPlaycount' => "1",
        'searchBar' => 'date',
        'form' => $queryForm->createView(),
      ]);
    }
}
