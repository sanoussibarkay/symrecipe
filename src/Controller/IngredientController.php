<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ExpressionLanguage\Expression;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


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
    #[IsGranted('ROLE_USER')]
    public function index(IngredientRepository $Repository, PaginatorInterface $paginator, Request $request): Response
    {

        $ingredients = $paginator->paginate(
            $Repository->findBy(['user' => $this->getUser()]), /* query NOT result */
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
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, \Doctrine\ORM\EntityManagerInterface $manager): Response
    {
        $ingredients = new \App\Entity\Ingredient();
        $form = $this->createForm(\App\Form\IngredientType::class, $ingredients);
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $ingredients->setUser($this->getUser());
                    $manager->persist($ingredients);
                    $manager->flush();
                    $this->addFlash('success', 'L\'ingrédient a été créé avec succès !');
                    return $this->redirectToRoute('app_ingredient');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement. veuillez vérifier la connexion');
                    return $this->redirectToRoute('ingredient.new');
                }
            } else {
                $this->addFlash('error', 'Le formulaire contient des erreurs.');
                return $this->redirectToRoute('ingredient.new');
            }
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
   #[IsGranted(
    attribute: new Expression('user === subject'),
    subject: new Expression('args["ingredient"].getUser()')
)]

    public function edit(
        \App\Entity\Ingredient $ingredient,
        Request $request,
        \Doctrine\ORM\EntityManagerInterface $manager
    ): Response {
  
        $form = $this->createForm(\App\Form\IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
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
