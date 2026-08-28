<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class SecurityController extends AbstractController
{


    #[Route('/connexion', name: 'security.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

      $lastUsername = $authenticationUtils->getLastUsername();
      $error = $authenticationUtils->getLastAuthenticationError();
         
        return $this->render('pages/security/login.html.twig', [
            'controller_name' => 'SecurityController',
            'lastUsername' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/deconnexion', name: 'security.logout', methods: ['GET'])]
     public function logout(): void
    {
       
    }
}
