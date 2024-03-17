<?php

namespace App\Controller;

use App\Entity\Import;
use App\Entity\Scrobble;
use App\Form\RegistrationType;
use App\Service\ApiRequestService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends CustomAbsrtactController
{

  protected ApiRequestService $apiRequestService;

  public function __construct(EntityManagerInterface $entityManager,ApiRequestService $apiRequestService)
  {
    parent::__construct($entityManager);
    $this->apiRequestService = $apiRequestService;
  }

  #[Route('/myAccount/import', name: 'app_import')]
  public function index(): Response
  {
    $paramView = ['selected' => 'import', 'dev' => $_ENV['APP_ENV'] === 'dev'];
    $user = $this->getUser();

    //Lastfm user info form
    $form = $this->createForm(RegistrationType::class, $this->getUser());
    $paramView['form'] = $form->createView();

    //Get import status
    $importStatusMessage = "Can't get import status";
    $importStatusProportion = 0;
    $importRepository = $this->entityManager->getRepository(Import::class);
    $lastImportTimestamp = $importRepository->getLastImportTimestamp($user);

    $this->apiRequestService->setUser($user);
    $apiResponse = $this->apiRequestService->getLastTracks($lastImportTimestamp);
    //TODO : handle error response from API
    $jsonResponse = json_decode($apiResponse, true);
    if ($jsonResponse !== false) {
      $scrobbleNotImported = $jsonResponse['recenttracks']['@attr']['total'];

      $scrobbleRepository = $this->entityManager->getRepository(Scrobble::class);
      $finalizedScrobble = $scrobbleRepository->getTotalScrobbleForUser($user);

      $importStatusMessage = $scrobbleNotImported . " scrobbles not imported yet";
      $totalScrobble = $scrobbleNotImported + $finalizedScrobble;
      $importStatusProportion = $finalizedScrobble * 100 / $totalScrobble;
    }


    //Get all imports
    $importRepository = $this->entityManager->getRepository(Import::class);
    $imports = $importRepository->findBy(['user' => $user], ['date' => 'DESC']);
    $imports = array_reverse($imports);

    foreach ($imports as $import) {
      if ($import->getTotalScrobble() > 0 && $import->getFinalizedScrobble() >= 0) {
        $inProgressPercent = $import->getFinalizedScrobble() * 100 / $import->getTotalScrobble();
        $import->setInProgress(round($inProgressPercent));
      } else {
        $import->setInProgress(0);
      }
    }

    $paramView['imports'] = $imports;
    $paramView['importStatusMessage'] = $importStatusMessage;
    $paramView['importStatusProportion'] = $importStatusProportion;

    return $this->render('import/index.html.twig', $paramView);
  }
}
