<?php

namespace App\MessageHandler;

use App\Entity\Image;
use App\Entity\Import;
use App\Entity\Scrobble;
use App\Message\ScrobbleImportMessage;
use App\Service\ApiRequestService;
use App\Service\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class ScrobbleImportMsgHandler
{

  //TODO : move exception constants
  const EXCEPTION_ERROR = 0;
  const EXCEPTION_NO_DATA = 1;

  const MAX_SCROBBLES_BY_IMPORT = 20;

  protected EntityManagerInterface $entityManager;
  protected EntityService $entityService;
  protected ApiRequestService $apiRequestService;
  protected Security $security;

  public function __construct(EntityManagerInterface $entityManager, Security $security, EntityService $entityService, ApiRequestService $apiRequestService)
  {
    $this->entityManager = $entityManager;
    $this->entityService = $entityService;
    $this->apiRequestService = $apiRequestService;
    $this->security = $security;
  }

  /**
   * Call the API for get the last scrobbles and import them in the database
   * @param ScrobbleImportMessage $message
   * @return mixed
   */
  public function __invoke(ScrobbleImportMessage $message)
  {

    $importedScrobbleNb = 0;
    $import = new Import();
    $user = null;

    try {

      //get last import
      $import->setDate(new \DateTime());
      $user = $this->security->getUser();
      $import->setUser($user);
      $import->setSuccessful(false);

      //Get last import
      $importRepository = $this->entityManager->getRepository(Import::class);
      $lastImportCollection = $importRepository->findBy(
        ['user' => $user, 'successful' => true],
        ['date' => 'DESC'],
        1
      );
      //TODO : Check if last import contains a scrobble and a datetime
      if (empty($lastImportCollection)) {
        $lastImportTimestamp = null;
      } else {
        $lastImportTimestamp = $lastImportCollection[0]->getLastScrobble()->getTimestamp();
      }

      //first API Call for get total pages and total scrobbles
      $apiResponse = $this->apiRequestService->getLastTracks($lastImportTimestamp);
      //TODO : handle error response from API
      $jsonResponse = json_decode($apiResponse, true);
      if ($jsonResponse === false) {
        throw new \Exception("Error in ScrobblerController::updateScrobble() : Can't decode api first response in json");
      }
      $totalPages = $jsonResponse['recenttracks']['@attr']['totalPages'];
      $totalScrobbles = $jsonResponse['recenttracks']['@attr']['total'];

      //The first scrobble can be in playing state, so we need to get the next scrobble
      $i = 0;
      while (!isset($jsonResponse['recenttracks']['track'][$i]['date']['uts'])) {
        $i++;
      }
      $timestampLimit = $jsonResponse['recenttracks']['track'][$i]['date']['uts'];

      //API Calls for get scrobbles segmented by pages of API
      for ($page = $totalPages; $page >= 1; $page--) {

        $apiResponse = $this->apiRequestService->getLastTracks($lastImportTimestamp, $timestampLimit, $page);

        //Parsing
        $jsonResponse = json_decode($apiResponse, true);
        if ($jsonResponse === false) {
          throw new \Exception("Error in ScrobblerController::updateScrobble() : Can't decode api response in json");
        }
        $scrobbles = $jsonResponse['recenttracks']['track'];
        if (empty($scrobbles)) {
          throw new \Exception("No data", self::EXCEPTION_NO_DATA);
        }

        //inverse array to get the oldest scrobble first
        $scrobbles = array_reverse($scrobbles);

        foreach ($scrobbles as $arrayScrobble) {

          //Max import limit capacity
          if ($importedScrobbleNb >= self::MAX_SCROBBLES_BY_IMPORT) {
            break 2;
          }

          //Don't add scrobble if it's in playing state
          if (isset($arrayScrobble['@attr']['nowplaying'])) {
            continue;
          }

          $scrobbleGenerated = $this->createScrobble($arrayScrobble);

          //Import
          $import->setLastScrobble($scrobbleGenerated);
          $this->entityManager->persist($import);

          $this->entityManager->flush();
          $importedScrobbleNb++;
        }
      }

      $import->setSuccessful(true);
      isset($scrobble) ? $import->setLastScrobble($scrobble) : null;
      $this->entityManager->persist($import);
      $this->entityManager->flush();

      $limitCapacityMessage = '';
      if ($importedScrobbleNb == self::MAX_SCROBBLES_BY_IMPORT) {
        $limitCapacityMessage = ' (The import has reached the maximum capacity of ' . self::MAX_SCROBBLES_BY_IMPORT . ' scrobbles, please import again to get the rest).';
      }
      return $this->render('scrobbler/index.html.twig', [
        'message' => $importedScrobbleNb . ' scrobbles added' . $limitCapacityMessage,
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

  /**
   * Create scrobble from array. Manage artist, album, track and image.
   * @param $lastFmScrobble
   * @return Scrobble
   */
  private function createScrobble($lastFmScrobble): Scrobble
  {

    //Artist
    $criteriaArtist = ['mbid' => $lastFmScrobble['artist']['mbid'], 'name' => $lastFmScrobble['artist']['#text']];
    $artist = $this->entityService->getExistingArtistOrCreateIt($criteriaArtist);
    if ($artist->getId() == 0) {
      $this->entityManager->persist($artist);
    }

    //Image
    $images = array();
    foreach ($lastFmScrobble['image'] as $jsonImage) {
      $size = Image::SIZE_UNDEFINED;
      if (array_key_exists($jsonImage['size'], Image::SIZES)) {
        $size = Image::SIZES[$jsonImage['size']];
      }
      $image = $this->entityService->getExistingImageOrCreateIt(['url' => $jsonImage['#text'], 'size' => $size]);
      if ($image->getId() == 0) {
        $this->entityManager->persist($image);
      }
      $images[] = $image;
    }

    //Album
    $criteriaAlbum = ['mbid' => $lastFmScrobble['album']['mbid'], 'name' => $lastFmScrobble['album']['#text'], 'artist' => $artist];
    $album = $this->entityService->getExistingAlbumOrCreateIt($criteriaAlbum, $images);
    if ($album->getId() == 0) {
      $this->entityManager->persist($album);
    }

    //Track
    $criteriaTrack = ['mbid' => $lastFmScrobble['mbid'], 'name' => $lastFmScrobble['name'], 'artist' => $artist, 'album' => $album, 'url' => $lastFmScrobble['url']];
    $track = $this->entityService->getExistingTrackOrCreateIt($criteriaTrack, $images);
    if ($track->getId() == 0) {
      $this->entityManager->persist($track);
    }

    //Scrobble
    $criteriaScrobble = ['track' => $track, 'timestamp' => $lastFmScrobble['date']['uts']];
    $scrobble = $this->entityManager->getRepository(Scrobble::class)->findOneBy($criteriaScrobble);
    if ($scrobble === null) {
      $scrobble = new Scrobble();
      $scrobble->setTrack($track);
      $scrobble->setTimestamp($lastFmScrobble['date']['uts']);
      $user = $this->security->getUser();
      $scrobble->setUser($user);
      $this->entityManager->persist($scrobble);
    }


    return $scrobble;
  }


  /**
   * Update the number of scrobbles in the import, persist it and flush it
   * @param Import $import
   * @param mixed $importedScrobbleNb
   * @return void
   */
  public function updateFinalizedScrobbleNb(Import $import, mixed $importedScrobbleNb)
  {
    if ($importedScrobbleNb != $import->getFinalizedScrobble()) {
      $import->setFinalizedScrobble($importedScrobbleNb);
      $this->entityManager->persist($import);
      $this->entityManager->flush();
    }
  }

}