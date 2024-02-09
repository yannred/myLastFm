<?php

namespace App\Repository;

use App\Entity\Scrobble;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

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
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Scrobble::class);
  }


  public function paginationQuery(): Query
  {
    return $this->createQueryBuilder('s')
      ->orderBy('s.id', 'ASC')
      ->getQuery();
  }
}
