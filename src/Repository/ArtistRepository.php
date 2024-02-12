<?php

namespace App\Repository;

use App\Entity\Artist;
use App\Entity\Scrobble;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Collection;
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


  public function getTop10Artists (): array {

    $user = $this->security->getUser();
    $artists = array();


    $artistTop10 = $this->createQueryBuilder('a')
      ->select('a.id, count(s.id) as count')
      ->join('a.tracks', 't')
      ->join('t.scrobbles', 's')
      ->where('s.user = :user')
      ->setParameter('user', $user->getId())
      ->groupBy('a.name')
      ->orderBy('count(a.name)', 'DESC')
      ->getQuery()
      ->getResult();

    foreach ($artistTop10 as $artist) {

      if (count($artists) >= 2) {
        break;
      }

      $artistEntity = $this->findOneBy(['id' => $artist['id']]);
      $artistEntity->setUserPlaycount($artist['count']);
      $artists[] = $artistEntity;
    }

    return $artists;
  }

}
