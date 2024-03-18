<?php

namespace App\Controller;

use App\Data\SearchBarData;
use App\Entity\Artist;
use App\Form\SearchBarType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyArtistsController extends CustomAbsrtactController
{
  const LIMIT_PER_PAGE = 20;

  /**
   * Render the page with the list of artists
   * @param Request $request
   * @param PaginatorInterface $paginator
   * @return Response
   */
  #[Route('/myPage/myArtists', name: 'app_my_artists')]
  public function index(Request $request, PaginatorInterface $paginator): Response
  {
    //TODO : use a GET request

    $artistRepository = $this->entityManager->getRepository(Artist::class);

    $searchBarData = new SearchBarData();
    $searchForm = $this->createForm(SearchBarType::class, $searchBarData);
    $searchForm->handleRequest($request);

    $query = $artistRepository->paginationFilteredQuery($searchBarData);

    $artistPagination = $paginator->paginate(
      $query,
      $request->query->getInt('page', 1),
      self::LIMIT_PER_PAGE
    );

    $response = new Response();
    if ($searchForm->isSubmitted() && $searchForm->isValid()) {
      $response->setStatusCode(Response::HTTP_SEE_OTHER);
    }

    return $this->render(
      'my_artists/index.html.twig',
      [
        'artists' => $artistPagination,
        'pagination' => "1",
        'userPlaycount' => "1",
        'searchBar' => 'date',
        'form' => $searchForm,
        'activeNavbarItem' => $request->get('_route'),
      ],
      $response);
  }
}
