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
    $userInfo = $apiRequestService->getUserInfo();
    //TODO : handle error response from API
    $userInfo = json_decode($userInfo, true);
    if ($userInfo === false) {
      throw new \Exception("Error in ScrobblerController::updateScrobble() : Can't decode api first response in json");
    }

    $userPageInfo = array();
    $userPageInfo['userName'] = $userInfo['user']['name'];
    $userPageInfo['userRealName'] = $userInfo['user']['realname'];
    $userPageInfo['scrobbleCount'] = $userInfo['user']['playcount'];
    foreach ($userInfo['user']['image'] as $image) {
      if ($image['size'] == 'large') {
        $userPageInfo['image'] = $image['#text'];
        break;
      }
    }
    $userPageInfo['trackCount'] = $userInfo['user']['track_count'];
    $userPageInfo['albumCount'] = $userInfo['user']['album_count'];
    $userPageInfo['artistCount'] = $userInfo['user']['artist_count'];


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
      'userPageInfo' => $userPageInfo,
      'scrobbles' => $scrobblePagination,
      'artists' => $artists,
      'pagination' => 0,
      'userPlaycount' => 1,
      'tracks' => $tracks
    ]);
  }
}