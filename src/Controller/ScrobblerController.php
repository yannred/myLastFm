<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Scrobble;
use App\Service\ApiRequest;
use App\Service\EntityService\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AllowDynamicProperties] class ScrobblerController extends AbstractController
{

  //TODO move exception constants
  const EXCEPTION_ERROR = 0;
  const EXCEPTION_NO_DATA = 1;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/scrobbler', name: 'app_scrobbler')]
  public function index(): Response
  {
    return $this->render('scrobbler/index.html.twig', [
      'controller_name' => 'ScrobblerController',
    ]);
  }


  #[Route('/updateScrobble', name: 'app_update_scrobble')]
  public function updateScrobble(EntityService $entityService, ApiRequest $apiRequest): Response
  {
    try {

      $artists = [];
      $albums = [];
      $images = [];
      $tracks = [];
      $scrobbles = [];

      $i = 0;

      $apiResponse = $apiRequest->getLastTracks();

      $jsonResponse = json_decode($apiResponse, true);


      if ($jsonResponse === false) {
        throw new \Exception("Error in ApiParsing::getRecentTracks() : Can't decode api response in json");
      }

      $scrobles = $jsonResponse['recenttracks']['track'];
      if (empty($scrobles)) {
        throw new \Exception("No data", self::EXCEPTION_NO_DATA);
      }

      foreach ($scrobles as $arrayScrobble) {

        $criteriaArtist = ['mbid' => $arrayScrobble['artist']['mbid'], 'name' => $arrayScrobble['artist']['#text']];
        $artist = $entityService->getExistingArtistOrCreateIt($criteriaArtist);

        $criteriaAlbum = ['mbid' => $arrayScrobble['album']['mbid'], 'name' => $arrayScrobble['album']['#text'], 'artist' => $artist];
        $album = $entityService->getExistingAlbumOrCreateIt($criteriaAlbum);

        $criteriaTrack = ['mbid' => $arrayScrobble['mbid'], 'name' => $arrayScrobble['name'], 'artist' => $artist, 'album' => $album, 'url' => $arrayScrobble['url']];
        $track = $entityService->getExistingTrackOrCreateIt($criteriaTrack);

        $criteriaScrobble = ['track' => $track, 'timestamp' => $arrayScrobble['date']['uts']];
        $scrobble = $this->entityManager->getRepository(Scrobble::class)->findOneBy($criteriaScrobble);
        if ($scrobble === null) {
          $scrobble = new Scrobble();
          $scrobble->setTrack($track);
          $scrobble->setTimestamp($arrayScrobble['date']['uts']);
          $this->entityManager->persist($scrobble);
          $this->entityManager->flush();
        }

        $i++;
      }

      return $this->render('scrobbler/index.html.twig', [
        'message' => 'nb scrobble integrés : ' . $i,
      ]);


    } catch (\Exception $e) {

      if ($e->getCode() === self::EXCEPTION_NO_DATA) {
        return $this->render('scrobbler/index.html.twig', [
          'message' => 'No data',
        ]);
      } else {
        return $this->render('scrobbler/index.html.twig', [
          'message' => $e->getMessage(),
        ]);
      }
    }

  }
}