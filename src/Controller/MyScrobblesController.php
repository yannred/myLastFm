<?php

namespace App\Controller;

use App\Data\SearchBarData;
use App\Entity\Scrobble;
use App\Form\SearchBarType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyScrobblesController extends CustomAbsrtactController
{
  const LIMIT_PER_PAGE = 20;

  #[Route('/myPage/myScrobbles', name: 'app_my_scrobbles')]
  public function index(Request $request, PaginatorInterface $paginator): Response
  {
    //TODO : use a GET request

    $response = new Response();
    $view = 'my_scrobbles/index.html.twig';

    $scrobbleRepository = $this->entityManager->getRepository(Scrobble::class);

    $searchBarData = new SearchBarData();
    $searchForm = $this->createForm(SearchBarType::class, $searchBarData);
    $searchForm->handleRequest($request);

    // TODO : Control the dates (on all pages with date search)
    //  error on timestamp convert if not respecting the format
    //  if only one date is set, the other is set to the curent date
    //  if the same date is set, the "to" date is set to the timestamp of the end of the day
    $query = $scrobbleRepository->paginationFilteredQuery($searchBarData);
    $scrobblePagination = $paginator->paginate(
      $query,
      $request->query->getInt('page', 1),
      self::LIMIT_PER_PAGE
    );

    $paramView = [
      'scrobbles' => $scrobblePagination,
      'form' => $searchForm->createView(),
      'pagination' => 1,
      'searchBar' => 'full',
      'activeNavbarItem' => $request->get('_route'),
      'tbodyUrl' => 'my_scrobbles/tbody.html.twig',
      'thead' => ['' , 'Title', 'Artist', 'Album', 'Date']
    ];

    if ($searchForm->isSubmitted() && $searchForm->isValid()) {
      $response->setStatusCode(Response::HTTP_SEE_OTHER);
    } else {
      $response = null;
    }

    return $this->render($view, $paramView, $response);
  }
}