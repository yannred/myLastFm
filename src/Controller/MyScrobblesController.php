<?php

namespace App\Controller;

use App\Entity\Scrobble;
use App\Form\QueryType;
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

  #[Route('/myAccount/myScrobbles', name: 'app_my_scrobbles')]
  public function index(Request $request, PaginatorInterface $paginator): Response
  {
    $queryForm = $this->createForm(QueryType::class);
    $queryForm->handleRequest($request);

    $scrobbleRepository = $this->entityManager->getRepository(Scrobble::class);

    $scrobblePagination = $paginator->paginate(
      $scrobbleRepository->paginationQuery(),
      $request->query->getInt('page', 1),
      self::LIMIT_PER_PAGE
    );

    return $this->render('my_scrobbles/index.html.twig', [
      'scrobbles' => $scrobblePagination,
      'form' => $queryForm->createView()
    ]);
  }
}