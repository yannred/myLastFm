<?php

namespace App\Controller;

use App\Entity\Scrobble;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LastScrobblesController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  const LIMIT_PER_PAGE = 20;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/myAccount/lastScrobbles', name: 'app_last_scrobbles')]
  public function index(Request $request, PaginatorInterface $paginator): Response
  {
    $scrobbleRepository = $this->entityManager->getRepository(Scrobble::class);
    $scrobbles = $scrobbleRepository->findAll();

    $pagination = $paginator->paginate(
      $scrobbleRepository->paginationQuery(),
      $request->query->getInt('page', 1),
      self::LIMIT_PER_PAGE
    );

    dump($pagination);








    return $this->render('last_scrobbles/index.html.twig', [
      'scrobbles' => $pagination,
    ]);
  }
}