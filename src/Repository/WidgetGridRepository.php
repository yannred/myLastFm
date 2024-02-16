<?php

namespace App\Repository;

use App\Entity\WidgetGrid;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WidgetGrid::class);
    }

//    /**
//     * @return WidgetGrid[] Returns an array of WidgetGrid objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('w.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?WidgetGrid
//    {
//        return $this->createQueryBuilder('w')
//            ->andWhere('w.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
