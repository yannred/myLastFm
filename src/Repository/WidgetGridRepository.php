<?php

namespace App\Repository;

use App\Entity\WidgetGrid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * @extends ServiceEntityRepository<WidgetGrid>
 *
 * @method WidgetGrid|null find($id, $lockMode = null, $lockVersion = null)
 * @method WidgetGrid|null findOneBy(array $criteria, array $orderBy = null)
 * @method WidgetGrid[]    findAll()
 * @method WidgetGrid[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WidgetGridRepository extends ServiceEntityRepository
{

  protected Security $security;

  public function __construct(ManagerRegistry $registry, Security $security)
  {
    parent::__construct($registry, WidgetGrid::class);
    $this->security = $security;
  }


  public function getNextPositionY()
  {
    $user = $this->security->getUser();

    $positionY = $this->createQueryBuilder('grid')
      ->distinct()
      ->select('widget.positionY')
      ->join('grid.widgets', 'widget')
      ->where('grid.user = :user')
      ->setParameter('user', $user->getId())
      ->getQuery()
      ->getResult();

    $maxPositionY = 0;
    foreach ($positionY as $pos) {
      if ($pos['positionY'] > $maxPositionY) {
        $maxPositionY = $pos['positionY'];
      }
    }

    return $maxPositionY + 1;

  }


}
