<?php

namespace App\MessageHandler;

use App\Entity\Image;
use App\Entity\Import;
use App\Entity\Scrobble;
use App\Entity\User;
use App\Message\ScrobbleImportMessage;
use App\Service\ApiRequestService;
use App\Service\EntityService;
use App\Service\UtilsService;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Message handler for importing scrobble task
 */
#[AsMessageHandler]
class ScrobbleImportMsgHandler
{

  const MAX_SCROBBLES_BY_IMPORT = 5000; // 5 thousands

  protected EntityManagerInterface $entityManager;
  protected UtilsService $utilsService;
  protected EntityService $entityService;
  protected ApiRequestService $apiRequestService;

  protected ?User $user;

  public function __construct(EntityManagerInterface $entityManager, UtilsService $utils, EntityService $entityService, ApiRequestService $apiRequestService)
  {
    $this->entityManager = $entityManager;
    $this->utilsService = $utils;
    $this->entityService = $entityService;
    $this->apiRequestService = $apiRequestService;

    $this->user = null;
  }

  /**
   * Call the API for get the last scrobbles and import them in the database
   * @param ScrobbleImportMessage $message
   */
  public function __invoke(ScrobbleImportMessage $message): void
  {
    $importedScrobbleNb = 0;

    try {

      sleep(5); // 30 seconds

      //Get user
      $userid = $message->userId;
      $user = $this->entityManager->getRepository(User::class)->find($userid);
      if ($user === null) {
        throw new \Exception("Error in ScrobbleImportMsgHandler : Can't find user with id " . $userid);
      }
      $this->setUser($user);

      //Get import
      $importRepository = $this->entityManager->getRepository(Import::class);
      $import = $importRepository->find($message->importId);

      if ($import === null) {
        throw new \Exception("Error in ScrobbleImportMsgHandler : Can't find import with id " . $message->importId);
      }

      //Get last finished import for get the last scrobble timestamp
      $importRepository = $this->entityManager->getRepository(Import::class);
      $lastImportCollection = $importRepository->findBy(
        ['user' => $user, 'finalized' => true, 'error' => false],
        ['date' => 'DESC'],
        1
      );

      if (empty($lastImportCollection)) {
        $lastImportTimestamp = null;
      } else {
        // Check if last import contains a scrobble and a datetime
        if ($lastImportCollection[0]->getLastScrobble() === null) {
          $lastImportTimestamp = null;
        } else {
          $lastImportTimestamp = $lastImportCollection[0]->getLastScrobble()->getTimestamp();
        }
      }

      //first API Call for get total pages and total scrobbles
      $this->utilsService->logDevInfo('*** Scrobble Import Info : first API call for get total pages and total scrobbles');
      $apiResponse = $this->apiRequestService->getLastTracks($lastImportTimestamp);
      //TODO : handle error response from API
      $jsonResponse = json_decode($apiResponse, true);
      if ($jsonResponse === false) {
        throw new \Exception("Error in ScrobbleImportMsgHandler : Can't decode api first response in json");
      }
      $totalPages = $jsonResponse['recenttracks']['@attr']['totalPages'];
      $totalScrobbles = $jsonResponse['recenttracks']['@attr']['total'];

      if ($totalScrobbles > self::MAX_SCROBBLES_BY_IMPORT) {
        $totalScrobbles = self::MAX_SCROBBLES_BY_IMPORT;
      }
      $import->setTotalScrobble($totalScrobbles);
      $import->setStarted(true);
      $this->entityManager->persist($import);
      $this->entityManager->flush();

      //The first scrobble can be in playing state, so we need to get the next scrobble
      $i = 0;
      while (!isset($jsonResponse['recenttracks']['track'][$i]['date']['uts'])) {
        $i++;
      }
      $timestampLimit = $jsonResponse['recenttracks']['track'][$i]['date']['uts'];

      //API Calls for get scrobbles segmented by pages of API
      for ($page = $totalPages; $page >= 1; $page--) {

        $this->utilsService->logDevInfo('*** Scrobble Import Info : API call for get scrobbles page ' . $page);

        $apiResponse = $this->apiRequestService->getLastTracks($lastImportTimestamp, $timestampLimit, $page);

        //Parsing
        $jsonResponse = json_decode($apiResponse, true);
        if ($jsonResponse === false) {
          throw new \Exception("Error in ScrobbleImportMsgHandler : Can't decode api response in json");
        }
        $scrobbles = $jsonResponse['recenttracks']['track'];
        if (! empty($scrobbles)) {
          //inverse array to get the oldest scrobble first
          $scrobbles = array_reverse($scrobbles);

          foreach ($scrobbles as $arrayScrobble) {

            $this->utilsService->logDevInfo('*** Scrobble Import Info : scrobble ' . $importedScrobbleNb . ' : ' . $arrayScrobble['name'] . ' - ' . $arrayScrobble['artist']['#text']);

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
            $importedScrobbleNb++;
            $import->setLastScrobble($scrobbleGenerated);
            $import->setFinalizedScrobble($importedScrobbleNb);
            $this->entityManager->persist($import);

            $this->entityManager->flush();
          }
        } else {
          $import->setFinalizedScrobble(0);
        }
      }

      $import->setFinalized(true);
      $this->entityManager->persist($import);
      $this->entityManager->flush();


    } catch (\Exception $e) {
//      ini_set('memory_limit', '-1');
//      $this->utilsService->logError('*** Scrobble Import Info : ' . $e->getMessage() . ' *** File : '  . $e->getFile() . ':' . $e->getLine() . ' /*/ Trace : ' . print_r($e->getTrace(), true));
      $this->utilsService->logError("*** Scrobble Import Error : " . $e->getMessage() . ' *** File : '  . $e->getFile() . ':' . $e->getLine());
      if (isset($import)){
        $this->utilsService->logDevInfo("*** Scrobble Import Info : trying to save error info in import");
        $import->setError(true);
        $import->setErrorMessage($e->getMessage());
        if (!$this->entityManager->isOpen()) {
          $this->entityManager = DriverManager::getConnection([], $this->entityManager->getConfiguration(), $this->entityManager->getEventManager());
//          $this->entityManager = $this->entityManager->create($this->entityManager->getConnection(), $this->entityManager->getConfiguration());
        }
        $this->entityManager->persist($import);
        $this->entityManager->flush();
        $this->utilsService->logDevInfo("*** Scrobble Import Info : error info saved in import");
      } else {
        $this->utilsService->logDevInfo("*** Scrobble Import Info :import is null, can't save error info in import");
      }

    }
  }

  /**
   * Create scrobble from array. Manage artist, album, track and image.
   * Persist but do not flush the scrobble or the related entities
   * @param $lastFmScrobble
   * @return Scrobble
   */
  private function createScrobble($lastFmScrobble): Scrobble
  {

    //Artist
    $this->utilsService->logDevInfo('*** Scrobble Import Info : createScrobble : artist : ' . $lastFmScrobble['artist']['#text']);
    $criteriaArtist = ['mbid' => $lastFmScrobble['artist']['mbid'], 'name' => $lastFmScrobble['artist']['#text']];
    $artist = $this->entityService->getExistingArtistOrCreateIt($criteriaArtist);
    if ($artist->getId() == 0) {
      $this->entityManager->persist($artist);
    }

    //Image
    $this->utilsService->logDevInfo('*** Scrobble Import Info : createScrobble : images');
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
    $this->utilsService->logDevInfo('*** Scrobble Import Info : createScrobble : album : ' . $lastFmScrobble['album']['#text']);
    $criteriaAlbum = ['mbid' => $lastFmScrobble['album']['mbid'], 'name' => $lastFmScrobble['album']['#text'], 'artist' => $artist];
    $album = $this->entityService->getExistingAlbumOrCreateIt($criteriaAlbum, $images);
    if ($album->getId() == 0) {
      $this->entityManager->persist($album);
    }

    //Track
    $this->utilsService->logDevInfo('*** Scrobble Import Info : createScrobble : track : ' . $lastFmScrobble['name']);
    $criteriaTrack = ['mbid' => $lastFmScrobble['mbid'], 'name' => $lastFmScrobble['name'], 'artist' => $artist, 'album' => $album, 'url' => $lastFmScrobble['url']];
    $track = $this->entityService->getExistingTrackOrCreateIt($criteriaTrack, $images);
    if ($track->getId() == 0) {
      $this->entityManager->persist($track);
    }

    //Scrobble
    $this->utilsService->logDevInfo('*** Scrobble Import Info : createScrobble : scrobble');
    $criteriaScrobble = ['track' => $track, 'timestamp' => $lastFmScrobble['date']['uts']];
    $scrobble = $this->entityManager->getRepository(Scrobble::class)->findOneBy($criteriaScrobble);
    if ($scrobble === null) {
      $scrobble = new Scrobble();
      $scrobble->setTrack($track);
      $scrobble->setTimestamp($lastFmScrobble['date']['uts']);
      $scrobble->setUser($this->getUser());
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

  public function setUser(?User $user): void
  {
    $this->user = $user;
    $this->apiRequestService->setUser($user);
    $this->entityService->setUser($user);
  }

  public function getUser(): ?User
  {
    return $this->user;
  }

}