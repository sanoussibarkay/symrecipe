<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\User;
use App\Form\UserPasswordType;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserController extends AbstractController
{

    #[Route('/utilisateur/edition/{id}', name: 'user.edit', methods: ['GET', 'POST'])]
    public function edit(User $user, 
    Request $request, 
    EntityManagerInterface $entityManager, 
    UserPasswordHasherInterface $hasher): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('security.login');
        }

        if($this->getUser() !== $user) {
        return $this->redirectToRoute('recipe.index');
        }
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($hasher->isPasswordValid($user, $form->getData()->getPlainPassword())) {
                # code...
                $user = $form->getData();
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('success', 'Votre compte a bien ete modifie !');
                return $this->redirectToRoute('recipe.index');

            }else {
                # code...
                $this->addFlash('suv,ccess', 'Votre mot de passe est incorrect');
                
            }
            
        }
          return $this->render('pages/user/edit.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }
  
#[Route('/utilisateur/edition-mot-de-passe/{id}', name: 'user.edit.password', methods: ['GET', 'POST'])]
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

            // Hasher le nouveau mot de passe
            $hashedPassword = $hasher->hashPassword(
                $user,
                $data['newPassword']
            );

            // Enregistrer le hash
            $user->setPassword($hashedPassword);

            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre mot de passe a bien été modifié !'
            );

            return $this->redirectToRoute('recipe.index');

        } else {

            $this->addFlash(
                'warning',
                'Votre mot de passe actuel est incorrect.'
            );
        }
    }

    return $this->render('pages/user/edit_password.html.twig', [
        'form' => $form->createView(),
    ]);
}

}