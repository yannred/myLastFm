<?php

namespace App\Controller;

use App\Data\Notification;
use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyAccountController extends CustomAbsrtactController
{

  protected Security $security;

  public function __construct(EntityManagerInterface $entityManager, Security $security)
  {
    parent::__construct($entityManager);
    $this->security = $security;
  }

  #[Route('/myAccount/updateUser', name: 'app_my_account_update_user')]
  public function updateUser(Request $request): Response
  {
    $view = 'my_account/index.html.twig';
    $notifications = [];
    $response = new Response();

    try {
      $user = $this->getUser();
      $userRepository = $this->entityManager->getRepository(User::class);
      $user = $userRepository->findOneBy(['email' => $user->getEmail()]);
      $hasedPassword = $user->getPassword();
//      $user->setPassword(date('Y-m-d'));

      if ($user === null) {
        throw new \Exception("Error in MyAccountController::updateUser() : Can't find user");
      }

      $form = $this->createForm(RegistrationType::class, $this->getUser(), ['action' => $this->generateUrl('app_my_account_update_user')]);
      $form->handleRequest($request);

      if ($form->isSubmitted()) {
        //TODO CONTROL FORM
        $user = $form->getData();
//        $user->setPassword($hasedPassword);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $notifications[] = new Notification('Your profile has been updated', 'success');
        $response->setStatusCode(Response::HTTP_SEE_OTHER);
      } else {
        $notifications[] = new Notification('Error during update', 'danger');
        $response->setStatusCode(Response::HTTP_SEE_OTHER);
      }

    } catch (\Exception $e) {
      $notifications[] = new Notification('Internal error : ' . $e->getMessage(), 'danger');
      $response->setStatusCode(Response::HTTP_SEE_OTHER);
    }

    $paramView['form'] = $form;
    $paramView['selected'] = 'lastfm';
    $paramView['notifications'] = $notifications;
    return $this->render($view, $paramView, $response);
  }

  /**
   * Display the user Page with the selected tab
   * @param string $tab selected tab
   * @return Response
   */
  #[Route('/myAccount/{tab}', name: 'app_account')]
  public function profilePage(string $tab): Response
  {
    $user = $this->security->getUser();
//    $user->setPassword(date('Y-m-d'));

    $form = $this->createForm(RegistrationType::class, $user, ['action' => $this->generateUrl('app_my_account_update_user')]);
    $paramView['form'] = $form->createView();

    $paramView['selected'] = $tab;
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