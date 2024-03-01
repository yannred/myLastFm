<?php

namespace App\Repository;

use App\Entity\Import;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @extends ServiceEntityRepository<Import>
 *
 * @method Import|null find($id, $lockMode = null, $lockVersion = null)
 * @method Import|null findOneBy(array $criteria, array $orderBy = null)
 * @method Import[]    findAll()
 * @method Import[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ImportRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Import::class);
  }


  /**
   * Get last import finished without error and return his last scrobble imported timestamp
   * @param $user
   * @return int|null
   */
  public function getLastImportTimestamp(UserInterface $user): ?int
  {
    $lastImportTimestamp = null;

    //Get last finished import for get the last scrobble timestamp
    $lastImportCollection = $this->findBy(
      ['user' => $user, 'finalized' => true, 'error' => false],
      ['date' => 'DESC'],
      1
    );

    if (! empty($lastImportCollection)) {
      // Check if last import contains a scrobble and a datetime
      if ($lastImportCollection[0]->getLastScrobble() !== null) {
        $lastImportTimestamp = $lastImportCollection[0]->getLastScrobble()->getTimestamp();
      }
    }

    return $lastImportTimestamp;
  }

}
