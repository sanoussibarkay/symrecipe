<?php

namespace App\Controller;

use App\Form\RegistrationType;
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
    

    #[Route('/inscription', name: 'security.registration', methods: ['GET', 'POST'])]
    public function registration(\Symfony\Component\HttpFoundation\Request $request, \Doctrine\ORM\EntityManagerInterface $entityManager): Response
    {
        $user = new \App\Entity\User();
        $user->setRoles(['ROLE_USER']);
        
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');  
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('security.login');   
            }else {
                
                $this->addFlash('error', 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer.');  
                return $this->redirectToRoute('security.registration');
            }
           

        }

        return $this->render('pages/security/registration.html.twig', [
            'controller_name' => 'SecurityController',
            'form' => $form->createView(),
        ]);
    }
}
