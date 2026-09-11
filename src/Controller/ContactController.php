<?php

namespace App\Controller;

use App\Service\MailService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request,  EntityManagerInterface $entityManager,MailService $mailservice): Response
    {
        $contact = new \App\Entity\Contact();
          if ($this->getUser()) {
                # code...
                $contact->setFullName($this->getUser()->getFullName())
                        ->setEmail($this->getUser()->getEmail());
              }
              
        $form = $this->createForm(\App\Form\ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $contact = $form->getData();
                    $entityManager->persist($contact);
                    $entityManager->flush();

                    // Envoi de l'email
                    $mailservice->sendEmail(
                         $contact->getEmail(),
                        $contact->getSubject(),
                         'emails/contact.html.twig',
                        [
                            'contact' => $contact,
                            ]
                         );
    
                    $this->addFlash('success', 'Votre message a bien ete envoye !');
                    return $this->redirectToRoute('app_contact');
                            
                }
                catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur lors du flush.'. $e->getMessage());
                    return $this->redirectToRoute('app_contact');
                }
           
            }else {
            $this->addFlash('error', 'Le formulaire contient des erreurs.');
            return $this->redirectToRoute('app_contact');
        }
           
        }

        return $this->render('pages/contact/index.html.twig', [
            'controller_name' => 'ContactController',
            'form' => $form->createView(),
        ]);
    }
}
