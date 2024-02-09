<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Entity\Import;
use App\Entity\Scrobble;
use App\Entity\User;
use App\Service\ApiRequestService;
use App\Service\EntityService\EntityService;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[AllowDynamicProperties] class ScrobblerController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  //TODO : move exception constants
  const EXCEPTION_ERROR = 0;
  const EXCEPTION_NO_DATA = 1;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }


  #[Route('/myAccount/updateScrobble', name: 'app_update_scrobble')]
  public function updateScrobble(EntityService $entityService, ApiRequestService $apiRequest): Response
  {
    $i = 0;
    $import = new Import();
    $user = null;

    try {

      //Get user data
      $currentUser = $this->getUser();
      $userRepository = $this->entityManager->getRepository(User::class);
      $user = $userRepository->findOneBy(['email' => $currentUser->getEmail()]);
      if ($user === null) {
        throw new \Exception("Error in ScrobblerController::updateScrobble() : Can't find user");
      }

      //prepare new import
      $import->setDate(new \DateTime());
      $import->setUser($user);
      $import->setSuccessful(false);

      //Get last import
      $importRepository = $this->entityManager->getRepository(Import::class);
      $lastImportCollection = $importRepository->findBy(
        ['user' => $user, 'successful' => true],
        ['date' => 'DESC'],
        1
      );
      if (empty($lastImportCollection)) {
        $lastImportTimestamp = null;
      } else {
        $lastImportTimestamp = $lastImportCollection[0]->getDate();
      }

      //first API Call for get total pages and total scrobbles
      $apiResponse = $apiRequest->getLastTracks($user, $lastImportTimestamp);
      //TODO : handle error response from API
      $jsonResponse = json_decode($apiResponse, true);
      if ($jsonResponse === false) {
        throw new \Exception("Error in ScrobblerController::updateScrobble() : Can't decode api first response in json");
      }
      $totalPages = $jsonResponse['recenttracks']['@attr']['totalPages'];
      $totalScrobbles = $jsonResponse['recenttracks']['@attr']['total'];

      //The first scrobble can be in playing state, so we need to get the next scrobble
      $j = 0;
      while(!isset($jsonResponse['recenttracks']['track'][$j]['date']['uts'])){
        $j++;
      }
      $timestampLimit = $jsonResponse['recenttracks']['track'][$j]['date']['uts'];

      //next API Calls for get scrobbles
      for ($page = 1; $page <= 6; $page++) {

        $apiResponse = $apiRequest->getLastTracks($user, $lastImportTimestamp, $timestampLimit, $page);

        //Parsing
        $jsonResponse = json_decode($apiResponse, true);
        if ($jsonResponse === false) {
          throw new \Exception("Error in ScrobblerController::updateScrobble() : Can't decode api response in json");
        }
        $scrobles = $jsonResponse['recenttracks']['track'];
        if (empty($scrobles)) {
          throw new \Exception("No data", self::EXCEPTION_NO_DATA);
        }

        foreach ($scrobles as $arrayScrobble) {

          //Don't add scrobble if it's in playing state
          if (isset($arrayScrobble['@attr']['nowplaying'])) {
            continue;
          }

          //Artist
          $criteriaArtist = ['mbid' => $arrayScrobble['artist']['mbid'], 'name' => $arrayScrobble['artist']['#text']];
          $artist = $entityService->getExistingArtistOrCreateIt($criteriaArtist);
          if ($artist->getId() == 0){
            $this->entityManager->persist($artist);
          }

          //Album
          $criteriaAlbum = ['mbid' => $arrayScrobble['album']['mbid'], 'name' => $arrayScrobble['album']['#text'], 'artist' => $artist];
          $album = $entityService->getExistingAlbumOrCreateIt($criteriaAlbum);
          if ($album->getId() == 0){
            $this->entityManager->persist($album);
          }

          //Track
          $criteriaTrack = ['mbid' => $arrayScrobble['mbid'], 'name' => $arrayScrobble['name'], 'artist' => $artist, 'album' => $album, 'url' => $arrayScrobble['url']];
          $track = $entityService->getExistingTrackOrCreateIt($criteriaTrack);
          if ($track->getId() == 0){
            $this->entityManager->persist($track);
          }

          //Scrobble
          $criteriaScrobble = ['track' => $track, 'timestamp' => $arrayScrobble['date']['uts']];
          $scrobble = $this->entityManager->getRepository(Scrobble::class)->findOneBy($criteriaScrobble);
          if ($scrobble === null) {
            $scrobble = new Scrobble();
            $scrobble->setTrack($track);
            $scrobble->setTimestamp($arrayScrobble['date']['uts']);
            $scrobble->setUser($user);
            $this->entityManager->persist($scrobble);
          }

          //Import
          $import->setLastScrobble($scrobble);
          $this->entityManager->persist($import);

          $this->entityManager->flush();
          $i++;
        }
      }

      $import->setSuccessful(true);
      isset($scrobble) ? $import->setLastScrobble($scrobble) : null;
      $this->entityManager->persist($import);
      $this->entityManager->flush();

      return $this->render('scrobbler/index.html.twig', [
        'message' => $i . ' scrobbles added',
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