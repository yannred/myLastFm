<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }


  #[Route('/register', name: 'app_register')]
  public function index(Request $request, UserPasswordHasherInterface $passwordHasher): Response
  {
    $user = new User();
    $response = new Response();
    $view = '';
    $paramView = [];

    $form = $this->createForm(RegistrationType::class, $user);
    $form->handleRequest($request);

    //returned form
    if ($form->isSubmitted() && $form->isValid()) {

      $user = $form->getData();

      //controls
      try {

        //uniqueness control
        if ($this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getEmail()])){
          throw new \Exception('Email already exists');
        }

        //All Controls OK
        $password = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($password);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $response->setStatusCode(Response::HTTP_CREATED);
        $view = 'register/success.html.twig';

      } catch (\Exception $e) {

        //Error during controls
        $response->setStatusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
        $view = 'register/index.html.twig';
        $paramView = [
          'form' => $form,
          'notification' => $e->getMessage()
        ];
      }


    } else {
      //Blank form
      $view = 'register/index.html.twig';
      $paramView = [
        'form' => $form->createView()
      ];
    }

    return $this->render($view, $paramView, $response);
  }
}
