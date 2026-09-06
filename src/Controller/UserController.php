<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class UserController extends AbstractController
{

    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
     #[IsGranted(
    attribute: new Expression('user === subject'),
    subject: new Expression('args["user"]')
)]
    public function edit(User $user, 
    Request $request, 
    EntityManagerInterface $entityManager, 
    UserPasswordHasherInterface $hasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Votre compte a bien ete modifie !');
                return $this->redirectToRoute('recipe.index');

            }else {
               
                $this->addFlash('error', 'Votre mot de passe est incorrect !');
                return $this->redirectToRoute('user.edit', ['id' => $user->getId()]);
                
            }
            
        }
        return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
        ]);
      }
    


  
#[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
 #[IsGranted(
    attribute: new Expression('user === subject'),
    subject: new Expression('args["user"]')
)]
public function editPassword(
    User $user,
    Request $request,
    UserPasswordHasherInterface $hasher,
    EntityManagerInterface $entityManager
): Response {
   

    if (!$this->getUser()) {
        return $this->redirectToRoute('security.login');
    }

    if ($this->getUser() !== $user) {
        return $this->redirectToRoute('recipe.index');
    }

    $form = $this->createForm(UserPasswordType::class);
    $form->handleRequest($request);

  if ($form->isSubmitted() && $form->isValid()) {
        $data = $form->getData();
        // Vérifier l'ancien mot de passe
        if ($hasher->isPasswordValid($user, $data['plainPassword'])) {
            $user->setPlainPassword($data['newPassword']);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre mot de passe a bien été modifié !'
            );

            return $this->redirectToRoute('recipe.index');

        } 
        else {
            

            $this->addFlash(
                'error',
                'Votre mot de passe actuel est incorrect.'
            );

            return $this->redirectToRoute('user.edit.password', ['id' => $user->getId()]);
        }

    }
    
    

    return $this->render('pages/user/edit_password.html.twig', [
        'form' => $form->createView(),
    ]);
 }



}