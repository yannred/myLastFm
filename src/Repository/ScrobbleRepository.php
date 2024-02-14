<?php

namespace App\Repository;

use App\Data\SearchBarData;
use App\Entity\Scrobble;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

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
      ->orderBy('s.id', 'ASC')
      ->getQuery();
  }

  public function paginationQueryByDate(int $from = 0, int $to = 0)
  {
    $user = $this->security->getUser();

    $query = $this->createQueryBuilder('s')
      ->where('s.user = :user')
      ->setParameter('user', $user->getId())
      ->orderBy('s.timestamp', 'ASC');

    if ($from !== 0 || $to !== 0) {
      $query->andWhere('s.timestamp BETWEEN :from AND :to')
        ->setParameter('to', $to)
        ->setParameter('from', $from);
    }

    return $query->getQuery();
  }

  public function searchByName(SearchBarData $dataSearchBar)
  {

    if (!isset($dataSearchBar->search) || $dataSearchBar->search === null || $dataSearchBar->search === '') {
      return $this->findAll();
    }

    $query = $this->createQueryBuilder('s')
      ->join('s.track', 't')
      ->where('t.name LIKE :search')
      ->setParameter('search', '%' . $dataSearchBar->search . '%');

    return $query->getQuery()->getResult();
  }

}
