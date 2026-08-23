<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Recipe;
use App\Form\RecipeType;
use Doctrine\ORM\EntityManagerInterface;

final class RecipeController extends AbstractController
{
   /**
    * This controller is used to display the list of recipes.
    * @param RecipeRepository $repository
    * @param PaginatorInterface $paginator
    * @param Request $request
    * @return Response
    * @Route("/recette", name="recipe.index", methods={"GET"})
    */
    #[Route('/recette', name: 'recipe.index', methods: ['GET'])]
    public function index(
        RecipeRepository $repository, 
        PaginatorInterface $paginator, 
        Request $request
        ): Response
    {
        $recipes = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }

    /**
     * This controller is used to create a new recipe.
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     * @Route("/recette/creation", name="recipe.new", methods={"GET", "POST"})
     */
    #[Route('/recette/creation', name: 'recipe.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();

            $this->addFlash('success', 'La recette a été créée avec succès !');

            return $this->redirectToRoute('recipe.index');
        }

        return $this->render('pages/recipe/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    
    /**
    * this function edit a recipe
    *@param Recipe $recipe
    *@param Request $request
    *@param EntityManagerInterface $manager
    *@return Response
        */

    #[Route('/recette/edition/{id}', name: 'recipe.edit', methods: ['GET', 'POST'])]
    public function edit(
        \App\Entity\Recipe $recipe,
          Request $request, 
         \Doctrine\ORM\EntityManagerInterface $manager
         ): Response
    {
        $form = $this->createForm(\App\Form\RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $recipe = $form->getData();
            $manager->persist($recipe);
            $manager->flush();  
            $this->addFlash('success', 'La recette a été modifiée avec succès !'); 
            return $this->redirectToRoute('recipe.index');
        }
        return $this->render('pages/recipe/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    /**
     * This controller is used to delete a recipe.
     * @param Recipe $recipe
     * @param EntityManagerInterface $manager
     * @return Response
     * @Route("/recette/suppression/{id}", name="recipe.delete", methods={"GET"})
     */
    #[Route('/recette/suppression/{id}', name: 'recipe.delete', methods: ['GET'])]
    public function delete(
        \App\Entity\Recipe $recipe,
        \Doctrine\ORM\EntityManagerInterface $manager
    ): Response {
        $manager->remove($recipe);
        $manager->flush();
        $this->addFlash('success', 'La recette a été supprimée avec succès !');
        return $this->redirectToRoute('recipe.index');
    }

}
