<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CustomAbsrtactController extends AbstractController
{

  protected EntityManagerInterface $entityManager;

  public function __construct(EntityManagerInterface $entityManager)
  {
    $this->entityManager = $entityManager;
  }

  protected function render(string $view, array $parameters = [], Response $response = null): Response
  {
    if ($this->getUser() !== null) {
      $user = $this->entityManager->getRepository(User::class)->fullLoad($this->getUser()->getId());
      $parameters['user'] = $user;
    }

    return parent::render($view, $parameters, $response);
  }


}
