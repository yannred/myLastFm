<?php

namespace App\Controller;

use App\Data\SearchBarData;
use App\Entity\Track;
use App\Form\SearchBarType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyTracksController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  const LIMIT_PER_PAGE = 20;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/myPage/myTracks', name: 'app_my_tracks')]
  public function index(Request $request, PaginatorInterface $paginator): Response
  {

    //TODO : use a GET request

    $trackRepository = $this->entityManager->getRepository(Track::class);

    $searchBarData = new SearchBarData();
    $searchForm = $this->createForm(SearchBarType::class, $searchBarData);
    $searchForm->handleRequest($request);

    $query = $trackRepository->paginationFilteredQuery($searchBarData);

    $tracksPagination = $paginator->paginate(
      $query,
      $request->query->getInt('page', 1),
      self::LIMIT_PER_PAGE
    );

    $response = new Response();
    if ($searchForm->isSubmitted() && $searchForm->isValid()) {
      $response->setStatusCode(Response::HTTP_SEE_OTHER);
    }

    return $this->render(
      'my_tracks/index.html.twig',
      [
        'tracks' => $tracksPagination,
        'pagination' => "1",
        'userPlaycount' => "1",
        'searchBar' => 'date',
        'form' => $searchForm->createView(),
        'activeNavbarItem' => $request->get('_route'),
      ],
      $response
    );
  }
}
