<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyAccountController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }


  #[Route('/myAccount', name: 'app_my_account')]
  public function index(): Response
  {
    return $this->render('my_account/index.html.twig');
  }


  #[Route('/myAccount/deleteAllScrobbles', name: 'app_my_account_delete_scrobbles')]
  public function deleteAllScrobbles(): Response
  {

    $view = '';
    $paramView = [];
    $response = new Response();

    try {

      //Get user data
      $currentUser = $this->getUser();
      $userRepository = $this->entityManager->getRepository(User::class);
      $user = $userRepository->findOneBy(['email' => $currentUser->getEmail()]);

      if ($user === null) {
        throw new \Exception("Error in MyAccountController::updateScrobble() : Can't find user");
      }

      //Delete all imports
      $userRepository = $this->entityManager->getRepository(User::class);
      $userRepository->deleteAllScrobbles($user);

      $response->setStatusCode(Response::HTTP_OK);
      $view = 'my_account/index.html.twig';
      $paramView = [
        'notification' => 'All your scrobbles have been deleted'
      ];


    } catch (\Exception $e) {

      $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
      $view = 'my_account/index.html.twig';
      $paramView = [
        'notification' => $e->getMessage()
      ];
    }

    return $this->render($view, $paramView, $response);
  }
}