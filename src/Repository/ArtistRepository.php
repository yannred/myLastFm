<?php

namespace App\Repository;

use App\Data\SearchBarData;
use App\Entity\Artist;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Artist>
 *
 * @method Artist|null find($id, $lockMode = null, $lockVersion = null)
 * @method Artist|null findOneBy(array $criteria, array $orderBy = null)
 * @method Artist[]    findAll()
 * @method Artist[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArtistRepository extends ServiceEntityRepository
{

  protected Security $security;

  public function __construct(ManagerRegistry $registry, Security $security)
  {
    parent::__construct($registry, Artist::class);
    $this->security = $security;
  }


  public function getTopArtists(): array
  {
    $user = $this->security->getUser();

    $artists = $this->createQueryBuilder('artist')
      ->select('artist, count(scrobble.id) as count')
      ->join('artist.tracks', 'track')
      ->join('track.scrobbles', 'scrobble')
      ->where('scrobble.user = :user')
      ->setParameter('user', $user->getId())
      ->groupBy('artist.name')
      ->orderBy('count(scrobble.id)', 'DESC')
      ->getQuery()
      ->getResult();

    return $artists;
  }


  public function paginationFilteredQuery(SearchBarData $dataSearchBar): Query
  {
    $user = $this->security->getUser();

    $query = $this->createQueryBuilder('a')
      ->select('a, count(s.id) as count, count(distinct t.id) as totalTrack, count(distinct al.id) as totalAlbum')
      ->join('a.tracks', 't')
      ->join('t.scrobbles', 's')
      ->join('t.album', 'al')
      ->where('s.user = :user')
      ->setParameter('user', $user->getId())
      ->groupBy('a.name')
      ->orderBy('count(a.name)', 'DESC');

    if ($dataSearchBar->from !== null || $dataSearchBar->to !== null) {
      $query
        ->andWhere('s.timestamp BETWEEN :from AND :to')
        ->setParameter('from', $dataSearchBar->from->getTimestamp())
        ->setParameter('to', $dataSearchBar->to->getTimestamp());
    }

    return $query->getQuery();
  }

}
