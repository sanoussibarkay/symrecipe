<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Form\RecipeType;
use App\Repository\MarkRepository;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\Paginator;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
        Request $request,

        ): Response
    {
        $recipes = $paginator->paginate(
            $repository->findBy(['user' => $this->getUser()]), /* query NOT result */
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index.html.twig', [
            'recipes' => $recipes,
        ]);
    }
    

    #[Route('/recette/public', name: 'recipe.index.public', methods: ['GET'])]
    public function indexPublic(
        RecipeRepository $repository, 
        PaginatorInterface $paginator, 
        Request $request,

        ): Response
    {
        $recipes = $paginator->paginate(
            $repository->findPublicRecipe(null), /* query NOT result */
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('pages/recipe/index_public.html.twig', [
            'recipes' => $recipes,
        ]);
    }
    /**
     * This controller is used to display a recipe.
     * @param Recipe $recipe
     * @return Response
     * @Route("/recette/{id}", name="recipe.show", methods={"GET"})
     */
    #[Route('/recette/{id}', name: 'recipe.show', methods: ['GET', 'POST'])]
      #[IsGranted(
    attribute: new Expression('subject === true'),
    subject: new Expression('args["recipe"].isPublic()')
)]
    public function show(Recipe $recipe, 
    Request $request, 
    EntityManagerInterface $entityManager,
    MarkRepository $markRepository

    ): Response
    {
        $mark = new \App\Entity\Mark();
        $form = $this->createForm(\App\Form\MarkType::class, $mark);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mark->setUser($this->getUser());
            $mark->setRecipe($recipe);
            $existingMark = $markRepository->findOneBy([
                'user' => $this->getUser(), 
                'recipe' => $recipe]);
            if ($existingMark) {
                $this->addFlash('error', 'Vous avez déjà noté cette recette.');
                return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
            }
            $entityManager->persist($mark);
        
            $entityManager->flush();
            $this->addFlash('success', 'Votre note a été enregistrée avec succès !');
            return $this->redirectToRoute('recipe.show', ['id' => $recipe->getId()]);
        }
        return $this->render('pages/recipe/show.html.twig', [
            'recipe' => $recipe,
            'form' => $form->createView(),
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
     #[IsGranted(
    attribute: new Expression('user === subject'),
    subject: new Expression('args["recipe"].getUser()')
)]
    public function new(Request $request, EntityManagerInterface $manager): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $recipe->setUser($this->getUser());
                    $manager->persist($recipe);
                    $manager->flush();
                    $this->addFlash('success', 'La recette a été créée avec succès !');
                    return $this->redirectToRoute('recipe.index');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de la création de la recette : veuillez vous connectez');
                    return $this->redirectToRoute('recipe.new');
                }
            }else {
                $this->addFlash('error', 'Le formulaire contient des erreurs. Veuillez vérifier les champs et réessayer.');
                return $this->redirectToRoute('recipe.new');
            }
        
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
     #[IsGranted(
    attribute: new Expression('user === subject'),
    subject: new Expression('args["recipe"].getUser()')
)]
    public function edit(
        \App\Entity\Recipe $recipe,
          Request $request, 
         \Doctrine\ORM\EntityManagerInterface $manager
         ): Response
    {
        $form = $this->createForm(\App\Form\RecipeType::class, $recipe);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
