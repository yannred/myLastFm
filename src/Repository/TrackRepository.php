<?php

namespace App\Repository;

use App\Entity\Track;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<Track>
 *
 * @method Track|null find($id, $lockMode = null, $lockVersion = null)
 * @method Track|null findOneBy(array $criteria, array $orderBy = null)
 * @method Track[]    findAll()
 * @method Track[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TrackRepository extends ServiceEntityRepository
{
  protected Security $security;

  public function __construct(ManagerRegistry $registry, Security $security)
  {
    parent::__construct($registry, Track::class);
    $this->security = $security;
  }

  public function getTop10Tracks()
  {

    define('LIMIT_TOP_TRACK', 10);

    $user = $this->security->getUser();
    $tracks = array();

    $trackTop10 = $this->createQueryBuilder('t')
      ->select('t.id, count(t.id) as count')
      ->join('t.scrobbles', 's')
      ->where('s.user = :user')
      ->setParameter('user', $user->getId())
      ->groupBy('t.id')
      ->orderBy('count(t.id)', 'DESC')
      ->getQuery()
      ->getResult();

    foreach ($trackTop10 as $track) {

      if (count($tracks) >= LIMIT_TOP_TRACK) {
        break;
      }

      $trackEntity = $this->findOneBy(['id' => $track['id']]);
      $trackEntity->setUserPlaycount($track['count']);
      $tracks[] = $trackEntity;
    }

    return $tracks;

  }

}
