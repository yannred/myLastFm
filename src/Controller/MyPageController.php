<?php

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Scrobble;
use App\Entity\Track;
use App\Service\ApiRequestService;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyPageController extends CustomAbsrtactController
{

  /**
   * Display the user's page
   * @param ApiRequestService $apiRequestService
   * @param Request $request
   * @param PaginatorInterface $paginator
   * @return Response
   * @throws \Exception
   */
  #[Route('/myPage', name: 'app_my_page')]
  public function index(ApiRequestService $apiRequestService, Request $request, PaginatorInterface $paginator): Response
  {
    $apiRequestService->setUser($this->getUser());

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
      MyScrobblesController::LIMIT_PER_PAGE
    );

    //top tracks
    $trackRepository = $this->entityManager->getRepository(Track::class);
    $tracks = $trackRepository->getTopTracks();
    $tracks = array_slice($tracks, 0, Track::LIMIT_TOP_TRACKS);

    //top artists
    $artistRepository = $this->entityManager->getRepository(Artist::class);
    $artists = $artistRepository->getTopArtists();
    $artists = array_slice($artists, 0, Artist::LIMIT_TOP_ARTIST);

    //top albums
    $albumRepository = $this->entityManager->getRepository(Album::class);
    $albums = $albumRepository->getTopAlbums();
    $albums = array_slice($albums, 0, Album::LIMIT_TOP_ALBUMS);




    return $this->render('my_page/index.html.twig', [
      'lastFmUser' => $lastFmUser,
      'scrobbles' => $scrobblePagination,
      'artists' => $artists,
      'albums' => $albums,
      'pagination' => 0,
      'userPlaycount' => 1,
      'tracks' => $tracks,
      'activeNavbarItem' => $request->get('_route'),
    ]);
  }
}