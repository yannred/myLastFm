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


  /**
   * Returns the next free position in the y-axis
   * @param WidgetGrid $widgetGrid
   * @return int
   */
  public function getNextPositionY(WidgetGrid $widgetGrid): int
  {
    $maxPositionY = 0;
    $height = 0;

    $positionY = $this->createQueryBuilder('grid')
      ->distinct()
      ->select('widget.positionY, widget.height')
      ->join('grid.widgets', 'widget')
      ->where('grid = :grid')
      ->setParameter('grid', $widgetGrid)
      ->getQuery()
      ->getResult()
    ;

    foreach ($positionY as $pos) {
      if ($pos['positionY'] >= $maxPositionY) {
        if ($pos['positionY'] == $maxPositionY && $pos['height'] > $height){
          $height = $pos['height'];
        }
        $maxPositionY = $pos['positionY'];
      }
    }

    return $maxPositionY + $height;
  }


}
