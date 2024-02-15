<?php

namespace App\Service\EntityService;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Image;
use App\Entity\Track;
use App\Service\ApiRequestService;
use Doctrine\ORM\EntityManagerInterface;

class EntityService
{

  private EntityManagerInterface $em;
  private ApiRequestService $apiRequestService;

  //TODO : add indexes to the database for the researched fields

  public function __construct(EntityManagerInterface $em, ApiRequestService $apiRequestService)
  {
    $this->em = $em;
    $this->apiRequestService = $apiRequestService;
  }

  /**
   * Get an existing artist or create it (without flush it and persist it, it's the caller's responsibility to do it)
   * @param array $criteria
   * @return Artist
   */
  public function getExistingArtistOrCreateIt(array $criteria): Artist
  {
    $repo = $this->em->getRepository(Artist::class);
    $artist = $repo->findOneBy($criteria);
    if (!$artist) {
      $artist = new Artist();
      $artist->setMbid($criteria['mbid']);
      $artist->setName($criteria['name']);

      $apiResponse = $this->apiRequestService->getArtistInfo($artist);
      //TODO : handle error response from API
      $jsonResponse = json_decode($apiResponse, true);
      if ($jsonResponse === false) {
        throw new \Exception("Error in ScrobblerController::updateScrobble() : Can't decode api first response in json");
      }
      $artist->setUrl($jsonResponse['artist']['url']);
      $artist->setListeners($jsonResponse['artist']['stats']['listeners']);
      $artist->setPlaycount($jsonResponse['artist']['stats']['playcount']);
      $artist->setBioSummary($jsonResponse['artist']['bio']['summary']);
      $artist->setBioContent($jsonResponse['artist']['bio']['content']);

      //images
      $images = [];
      foreach ($jsonResponse['artist']['image'] as $image) {
        if (array_key_exists($image['size'], Image::SIZES)) {
          $size = Image::SIZES[$image['size']];
          $imageEntity = $this->getExistingImageOrCreateIt(['url' => $image['#text'], 'size' => $size]);
          if ($imageEntity->getId() == 0) {
            $this->em->persist($imageEntity);
          }
          $images[] = $imageEntity;
        }
      }
      foreach ($images as $image) {
        $artist->addImage($image);
      }


    }

    return $artist;
  }

  /**
   * Get an existing album or create it (without flush it and persist it, it's the caller's responsibility to do it)
   * @param array $criteria
   * @return Album
   */
  public function getExistingAlbumOrCreateIt(array $criteria, array $images): Album
  {
    $repo = $this->em->getRepository(Album::class);
    $album = $repo->findOneBy($criteria);
    if (!$album) {
      $album = new Album();
      $album->setMbid($criteria['mbid']);
      $album->setName($criteria['name']);
      $album->setArtist($criteria['artist']);
      foreach ($images as $image) {
        $album->addImage($image);
      }
    }

    return $album;
  }

  /**
   * Get an existing track or create it (without flush it and persist it, it's the caller's responsibility to do it)
   * @param array $criteria
   * @return Track
   */
  public function getExistingTrackOrCreateIt(array $criteria, array $images): Track
  {
    $repo = $this->em->getRepository(Track::class);
    $track = $repo->findOneBy($criteria);
    if (!$track) {
      $track = new Track();
      $track->setMbid($criteria['mbid']);
      $track->setName($criteria['name']);
      $track->setArtist($criteria['artist']);
      $track->setAlbum($criteria['album']);
      $track->setUrl($criteria['url']);
      foreach ($images as $image) {
        $track->addImage($image);
      }
    }

    return $track;
  }

  /**
   * Get an existing image or create it (without flush it and persist it, it's the caller's responsibility to do it)
   * @param array $criteria
   * @return void
   */
  public function getExistingImageOrCreateIt(array $criteria)
  {
    $repo = $this->em->getRepository(Image::class);
    $image = $repo->findOneBy($criteria);
    if (!$image) {
      $image = new Image();
      $image->setUrl($criteria['url']);
      $image->setSize($criteria['size']);
    }

    return $image;
  }

}