<?php

namespace App\Controller;

use App\Data\Notification;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyAccountController extends CustomAbsrtactController
{

  /**
   * Display the user Page with Profile selected
   * @return Response
   */
  #[Route('/myAccount/profile', name: 'app_profile')]
  public function profilePage(): Response
  {
    $paramView['selected'] = 'profile';
    return $this->render('my_account/index.html.twig', $paramView);
  }

  /**
   * Display the user Page with Settings selected
   * @return Response
   */
  #[Route('/myAccount/settings', name: 'app_settings')]
  public function settingsPage(): Response
  {
    $paramView['selected'] = 'settings';
    return $this->render('my_account/index.html.twig', $paramView);
  }


  /**
   * Delete all scrobbles and imports of the user
   * @return Response
   */
  #[Route('/myAccount/deleteAllScrobbles', name: 'app_my_account_delete_scrobbles')]
  public function deleteAllScrobbles(): Response
  {
    $view = 'my_account/index.html.twig';
    $notifications = [];
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
      $notifications[] = new Notification('All your scrobbles have been deleted', 'success');


    } catch (\Exception $e) {
      $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
      $notifications[] = new Notification('Internal error : ' . $e->getMessage(), 'danger');
    }

    $paramView['notifications'] = $notifications;
    return $this->render($view, $paramView, $response);
  }
}