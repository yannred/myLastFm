<?php

namespace App\Controller;

use App\Data\SearchBarData;
use App\Entity\Scrobble;
use App\Form\SearchBarType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyScrobblesController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  const LIMIT_PER_PAGE = 20;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/myPage/myScrobbles', name: 'app_my_scrobbles')]
  public function index(Request $request, PaginatorInterface $paginator): Response
  {
    $response = new Response();
    $view = 'my_scrobbles/index.html.twig';
    $paramView = [];

    $scrobbleRepository = $this->entityManager->getRepository(Scrobble::class);

    $searchBarData = new SearchBarData();
    $queryForm = $this->createForm(SearchBarType::class, $searchBarData);
    $queryForm->handleRequest($request);

    //returned search form
    if ($queryForm->isSubmitted() && $queryForm->isValid()) {

      // TODO : Control the dates
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
        'form' => $queryForm->createView(),
        'pagination' => 1,
      ];
      $response->setStatusCode(Response::HTTP_SEE_OTHER);

    } else {
      //Blank search form
      $scrobblePagination = $paginator->paginate(
        $scrobbleRepository->paginationQuery(),
        $request->query->getInt('page', 1),
        self::LIMIT_PER_PAGE
      );

      $paramView = [
        'scrobbles' => $scrobblePagination,
        'form' => $queryForm->createView(),
        'pagination' => 1,
        'searchBar' => 'full',
      ];
      $response = null;

    }

    return $this->render($view, $paramView, $response);
  }
}