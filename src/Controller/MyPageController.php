<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Entity\Scrobble;
use App\Entity\Track;
use App\Service\ApiRequestService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyPageController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/myPage', name: 'app_my_page')]
  public function index(ApiRequestService $apiRequestService, Request $request, PaginatorInterface $paginator): Response
  {

    //User infos
    $lastFmUserInfo = $apiRequestService->getLastFmUserInfo();
    //TODO : handle error response from API
    $lastFmUserInfo = json_decode($lastFmUserInfo, true);
    if ($lastFmUserInfo === false) {
      throw new \Exception("Error in ScrobblerController::updateScrobble() : Can't decode api first response in json");
    }

    $lastFmUser = array();
    $lastFmUser['userName'] = $lastFmUserInfo['user']['name'];
    $lastFmUser['userRealName'] = $lastFmUserInfo['user']['realname'];
    $lastFmUser['scrobbleCount'] = $lastFmUserInfo['user']['playcount'];
    foreach ($lastFmUserInfo['user']['image'] as $image) {
      if ($image['size'] == 'large') {
        $lastFmUser['image'] = $image['#text'];
        break;
      }
    }
    $lastFmUser['trackCount'] = $lastFmUserInfo['user']['track_count'];
    $lastFmUser['albumCount'] = $lastFmUserInfo['user']['album_count'];
    $lastFmUser['artistCount'] = $lastFmUserInfo['user']['artist_count'];


    //Last scrobbles
    $scrobbleRepository = $this->entityManager->getRepository(Scrobble::class);
    $scrobblePagination = $paginator->paginate(
      $scrobbleRepository->paginationQuery(),
      $request->query->getInt('page', 1),
      LastScrobblesController::LIMIT_PER_PAGE
    );

    //artists
    $artistRepository = $this->entityManager->getRepository(Artist::class);
    $artists = $artistRepository->getTop10Artists();

    //tracks
    $trackRepository = $this->entityManager->getRepository(Track::class);
    $tracks = $trackRepository->getTop10Tracks();


    return $this->render('my_page/index.html.twig', [
      'lastFmUser' => $lastFmUser,
      'scrobbles' => $scrobblePagination,
      'artists' => $artists,
      'pagination' => 0,
      'userPlaycount' => 1,
      'tracks' => $tracks
    ]);
  }
}