<?php

namespace App\Controller;

use App\Repository\RecipeIngredientRepository;
use App\Service\RecipesGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SavedRecipeController extends AbstractController
{
    /**
     * @Route("/saved/recipe", name="saved_recipe")
     */
    public function index(
        RecipesGenerator $generator,
        RecipeIngredientRepository $ingredientRepository
    ) {
        if ($this->getUser()) {
            $user = $this->getUser();

            $savedRecipeIds = json_decode(json_encode($user->getRecipeIds()), true);

            $selectedTagRecipes = $generator->getGeneratedRecipes($savedRecipeIds);

            $summedRecipes = $ingredientRepository->findSum($savedRecipeIds);

            return $this->render('saved_recipe/index.html.twig', [
                'selectedRecipes' => $selectedTagRecipes,
                'summedRecipes' => $summedRecipes,
            ]);
        }
        return $this->redirectToRoute('login');
    }
}
