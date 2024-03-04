<?php

namespace App\Repository;

use App\Data\SearchBarData;
use App\Entity\Scrobble;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Scrobble>
 *
 * @method Scrobble|null find($id, $lockMode = null, $lockVersion = null)
 * @method Scrobble|null findOneBy(array $criteria, array $orderBy = null)
 * @method Scrobble[]    findAll()
 * @method Scrobble[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ScrobbleRepository extends ServiceEntityRepository
{

  protected Security $security;

  public function __construct(ManagerRegistry $registry, Security $security)
  {
    parent::__construct($registry, Scrobble::class);
    $this->security = $security;
  }


  public function paginationQuery(): Query
  {
    $user = $this->security->getUser();

    return $this->createQueryBuilder('s')
      ->where('s.user = :user')
      ->setParameter('user', $user->getId())
      ->orderBy('s.timestamp', 'DESC')
      ->getQuery();
  }

  public function paginationFilteredQuery(SearchBarData $dataSearchBar): Query
  {
    $user = $this->security->getUser();

    $dateFilter = false;
    $trackFilter = false;
    $artistFilter = false;
    $albumFilter = false;

    if ($dataSearchBar->from !== null || $dataSearchBar->to !== null) {
      $dateFilter = true;
    }
    if ($dataSearchBar->track !== '') {
      $trackFilter = true;
    }
    if ($dataSearchBar->artist !== '') {
      $artistFilter = true;
    }
    if ($dataSearchBar->album !== '') {
      $albumFilter = true;
    }

    $query = $this->createQueryBuilder('s')
      ->join('s.track', 't')
      ->where('s.user = :user')
      ->setParameter('user', $user->getId())
      ->orderBy('s.timestamp', 'ASC');

    if ($dateFilter) {
      $query
        ->andWhere('s.timestamp BETWEEN :from AND :to')
        ->setParameter('from', $dataSearchBar->from->getTimestamp())
        ->setParameter('to', $dataSearchBar->to->getTimestamp());
    }

    if ($trackFilter) {
      $query
        ->andWhere('t.name LIKE :trackName')
        ->setParameter('trackName', '%' . trim($dataSearchBar->track) . '%');
    }

    if ($artistFilter) {
      $query
        ->join('t.artist', 'artist')
        ->andWhere('artist.name LIKE :artistName')
        ->setParameter('artistName', '%' . trim($dataSearchBar->artist) . '%');
    }

    if ($albumFilter) {
      $query
        ->join('t.album', 'album')
        ->andWhere('album.name LIKE :albumName')
        ->setParameter('albumName', '%' . trim($dataSearchBar->album) . '%');
    }

    $query->orderBy('s.timestamp', 'DESC');

    return $query->getQuery();
  }

  /**
   * Get total scrobble imported for a user
   * @param UserInterface $user
   * @return mixed
   */
  public function getTotalScrobbleForUser(UserInterface $user)
  {
    $result = $this->createQueryBuilder('s')
      ->select('count(s.id) as count')
      ->where('s.user = :user')
      ->setParameter('user', $user->getId())
      ->getQuery()
      ->getResult();

    return $result[0]['count'];
  }

}
