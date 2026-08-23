<?php

namespace App\Controller;

use App\Repository\IngredientRepository;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

final class IngredientController extends AbstractController
{
    /**
    * this function display the list of ingredients
    *@param IngredientRepository $Repository
    *@param PaginatorInterface $paginator
    *@param Request $request
    *@return Response
     */
    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET'])]
    public function index(IngredientRepository $Repository, PaginatorInterface $paginator, Request $request): Response
    {
        
    $ingredients = $paginator->paginate(
         $Repository->findAll(),
        $request->query->getInt('page', 1), /* page number */
        10 /* limit per page */
    );
       
        
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients,
        ]);
    }

    /**
    * this function create a new ingredient
    *@param Request $request
    *@param EntityManagerInterface $manager
    *@return Response
     */
    #[Route('/ingredient/nouveau', name: 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(Request $request, \Doctrine\ORM\EntityManagerInterface $manager): Response
    {
        $ingredients = new \App\Entity\Ingredient();
        $form = $this->createForm(\App\Form\IngredientType::class, $ingredients);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredients = $form->getData();
            
            $manager->persist($ingredients);
            $manager->flush();  
            $this->addFlash('success', 'L\'ingrédient a été créé avec succès !'); 
            return $this->redirectToRoute('app_ingredient');
        }
        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
    * this function edit an ingredient
    *@param Ingredient $ingredient
    *@param Request $request
    *@param EntityManagerInterface $manager
    *@return Response
        */

    #[Route('/ingredient/edition/{id}', name: 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(
        \App\Entity\Ingredient $ingredient,
          Request $request, 
         \Doctrine\ORM\EntityManagerInterface $manager
         ): Response
    {
        $form = $this->createForm(\App\Form\IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();
            $manager->persist($ingredient);
            $manager->flush();  
            $this->addFlash('success', 'L\'ingrédient a été modifié avec succès !'); 
            return $this->redirectToRoute('app_ingredient');
        }
        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/ingredient/suppression/{id}', name: 'ingredient.delete', methods: ['GET'])]
    public function delete(
        \App\Entity\Ingredient $ingredient,
        \Doctrine\ORM\EntityManagerInterface $manager
    ): Response {
        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash('success', 'L\'ingrédient a été supprimé avec succès !');
        return $this->redirectToRoute('app_ingredient');
    }
 
}
