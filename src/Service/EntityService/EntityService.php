<?php

namespace App\Service\EntityService;

use App\Entity\Album;
use App\Entity\Artist;
use App\Entity\Track;
use Doctrine\ORM\EntityManagerInterface;

class EntityService
{

  private EntityManagerInterface $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

  /**
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
      $this->em->persist($artist);
      $this->em->flush();
    }

    return $artist;
  }

  public function getExistingAlbumOrCreateIt(array $criteria): Album
  {
    $repo = $this->em->getRepository(Album::class);
    $album = $repo->findOneBy($criteria);
    if (!$album) {
      $album = new Album();
      $album->setMbid($criteria['mbid']);
      $album->setName($criteria['name']);
      $album->setArtist($criteria['artist']);
      $this->em->persist($album);
      $this->em->flush();
    }

    return $album;
  }

  public function getExistingTrackOrCreateIt(array $criteria): Track
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
      $this->em->persist($track);
      $this->em->flush();
    }

    return $track;
  }

}