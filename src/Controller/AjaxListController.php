<?php

namespace App\Controller;

use App\Repository\RecipeIngredientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AjaxListController extends AbstractController
{
    /**
     * @Route("/ajax/list", name="ingredient_list")
     */
    public function index(RecipeIngredientRepository $ingredientRepository)
    {
        $generatedRecipeIds = $this->container->get('session')->get('generatedRecipeIds');
        $summedRecipes = $ingredientRepository->findSum($generatedRecipeIds);

        return $this->render('ingredient_list/index.html.twig', [
            'ingredientList' => $summedRecipes,
        ]);
    }
}
