<?php

namespace App\Controller;

use App\Data\Notification;
use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends CustomAbsrtactController
{

  #[Route('/register', name: 'app_register')]
  public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
  {
    $user = new User();
    $response = new Response();
    $view = 'register/index.html.twig';
    $notifications = [];

    $form = $this->createForm(RegistrationType::class, $user);
    $form->handleRequest($request);
    $paramView = ['form' => $form];

    //returned form
    if ($form->isSubmitted() && $form->isValid()) {

      /** @var User $user */
      $user = $form->getData();

      //controls
      try {
        $success = true;

        //uniqueness control
        if ($this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()])) {
          $notifications[] = new Notification('Email already exists', 'warning');
          $success = false;
        }

        if ($success) {

          //All Controls OK

          //TODO : Get lastfm data

          $password = $passwordHasher->hashPassword($user, $user->getPassword());
          $user->setPassword($password);
          $this->entityManager->persist($user);
          $this->entityManager->flush();
          $view = 'register/success.html.twig';
          $response->setStatusCode(Response::HTTP_CREATED);
        } else {

          //Error during controls
          $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        }

      } catch (\Exception $e) {
        $response->setStatusCode(Response::HTTP_INTERNAL_SERVER_ERROR);
        $notifications[] = new Notification('Internal error : ' . $e->getMessage(), 'danger');
      }
    }

    $paramView['notifications'] = $notifications;
    return $this->render($view, $paramView, $response);
  }
}
