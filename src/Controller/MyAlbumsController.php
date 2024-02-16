<?php

namespace App\Controller;

use App\Data\SearchBarData;
use App\Entity\Album;
use App\Form\SearchBarType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyAlbumsController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  const LIMIT_PER_PAGE = 20;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/myPage/myAlbums', name: 'app_my_albums')]
  public function index(Request $request, PaginatorInterface $paginator): Response
  {
    $albumRepository = $this->entityManager->getRepository(Album::class);

    $searchBarData = new SearchBarData();
    $queryForm = $this->createForm(SearchBarType::class, $searchBarData);
    $queryForm->handleRequest($request);

    $query = $albumRepository->paginationFilteredQuery($searchBarData);

    $artistPagination = $paginator->paginate(
      $query,
      $request->query->getInt('page', 1),
      self::LIMIT_PER_PAGE
    );

    $response = new Response();
    if ($queryForm->isSubmitted() && $queryForm->isValid()) {
      $response->setStatusCode(Response::HTTP_SEE_OTHER);
    }

    return $this->render(
      'my_albums/index.html.twig',
      [
        'albums' => $artistPagination,
        'pagination' => "1",
        'userPlaycount' => "1",
        'searchBar' => 'date',
        'form' => $queryForm->createView(),
      ],
      $response
    );
  }
}