<?php

namespace App\Controller;

use AllowDynamicProperties;
use App\Data\Notification;
use App\Entity\Import;
use App\Message\ScrobbleImportMessage;
use App\Service\ApiRequestService;
use App\Service\EntityService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\MessageHandler\ScrobbleImportMsgHandler;

/**
 * Manage the scrobbles import
 */
#[AllowDynamicProperties]
class ScrobblerController extends AbstractController
{

  protected EntityManagerInterface $entityManager;
  protected Security $security;

  public function __construct(EntityManagerInterface $entityManager, Security $security)
  {
    $this->entityManager = $entityManager;
    $this->security = $security;
  }

  /**
   * Dispatch a new ScrobbleImportMessage for prepare the import of scrobbles
   * @see ScrobbleImportMsgHandler
   * @param MessageBusInterface $msgBus
   * @return Response
   */
  #[Route('/myAccount/updateScrobble', name: 'app_update_scrobble')]
  public function updateScrobble(MessageBusInterface $msgBus): Response
  {
    $user = $this->security->getUser();

    //check if there is an import in progress
    $importRepository = $this->entityManager->getRepository(Import::class);
    $lastImportCollection = $importRepository->findBy(['user' => $user->getId(), 'finalized' => false, 'error' => false]);
    if (!empty($lastImportCollection)) {
      return $this->render('scrobbler/task_added.html.twig', ['notifications' => [new Notification('An import is already in progress', 'warning')]]);
    }

    $import = new Import();
    $import->setDate(new \DateTime());
    $import->setUser($user);
    $this->entityManager->persist($import);
    $this->entityManager->flush();
    $msgBus->dispatch(new ScrobbleImportMessage($this->getUser()->getId(), $import->getId()));

    $notifications[] = new Notification('Task for importing scrobbles has been dispatched', 'success');

    return $this->render('scrobbler/task_added.html.twig', ['notifications' => $notifications]);
  }


  /**
   * Launch the import of scrobbles without using the queue
   * @return Response
   */
  #[Route('/myAccount/updateScrobbleNow', name: 'app_update_scrobble_now')]
  public function updateScrobbleWithoutQueue(LoggerInterface $logger, EntityService $entityService, ApiRequestService $apiRequestService): Response
  {
    $user = $this->security->getUser();

    $import = new Import();
    $import->setDate(new \DateTime());
    $import->setUser($user);
    $this->entityManager->persist($import);
    $this->entityManager->flush();
    $handler = new ScrobbleImportMsgHandler($this->entityManager, $logger,  $entityService, $apiRequestService);
    $handler->__invoke(new ScrobbleImportMessage($this->getUser()->getId(), $import->getId()));

    return $this->render('scrobbler/task_added.html.twig', ['notifications' => [new Notification('Task finished', 'success')]]);
  }

}