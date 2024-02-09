<?php

namespace App\Controller;

use App\Entity\Import;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImportController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  #[Route('/myAccount/import', name: 'app_import')]
  public function index(): Response
  {

    //Get all imports
    $importRepository = $this->entityManager->getRepository(Import::class);
    $imports = $importRepository->findAll();

    return $this->render('import/index.html.twig', [
      'controller_name' => 'ImportController',
      'imports' => $imports
    ]);
  }
}
