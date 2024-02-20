<?php

namespace App\Repository;

use App\Entity\Widget;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Widget>
 *
 * @method Widget|null find($id, $lockMode = null, $lockVersion = null)
 * @method Widget|null findOneBy(array $criteria, array $orderBy = null)
 * @method Widget[]    findAll()
 * @method Widget[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WidgetRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Widget::class);
  }

  public function createWidgetQuery(array $parameters, $user): Query
  {
    $queryBuilder = $this->getEntityManager()->getRepository($parameters['entity'])->createQueryBuilder($parameters['entityAlias']);

    if (isset($parameters['select'])) {
      $queryBuilder
        ->select($parameters['select']);
    } else {
      $queryBuilder
        ->select('scrobble');
    }

    if (isset($parameters['join'])) {
      foreach ($parameters['join'] as $join => $alias) {
        $queryBuilder
          ->join($join, $alias);
      }
    }

    $queryBuilder
      ->where('scrobble.user = ' . $user->getId())
    ;

    if (isset($parameters['where'])) {
      foreach ($parameters['where'] as $key => $value) {
        if ($key == 'and') {
          $queryBuilder
            ->andWhere($key . ' = ' . $value['value']);

        } else {
          $queryBuilder
            ->orWhere($key . ' = ' . $value['value']);
        }
        if (isset($value['parameter'])) {
          $queryBuilder
            ->setParameter($value['parameter'], $value['value']);
        }
      }
    }

    if (isset($parameters['groupby'])) {
      $queryBuilder
        ->groupBy($parameters['groupby']);
    }

    if (isset($parameters['orderby'])) {
      foreach ($parameters['orderby'] as $column => $order) {
        $queryBuilder
          ->orderBy($column, $order);
      }
    }

    return $queryBuilder->getQuery();
  }

}
