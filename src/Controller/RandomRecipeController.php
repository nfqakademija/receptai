<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RandomRecipeGenerator;
use Symfony\Contracts\Translation\TranslatorInterface;

class RandomRecipeController extends AbstractController
{
    /**
     * @Route("/random/recipe", name="random_recipe")
     */
    public function random(RandomRecipeGenerator $recipeGenerator, TranslatorInterface $translator)
    {
        if ($this->getUser()) {
            $recipeCount = $this->getDoctrine()
                ->getRepository(Recipe::class)
                ->findRecipeCount();

            $neededRecipesId = $recipeGenerator->getRecipeIdArray($recipeCount);

            $recipes = $this->getDoctrine()->getRepository(RecipeIngredient::class)->findBy([
                'recipe' => $neededRecipesId
            ]);
            $names = $this->getDoctrine()->getRepository(Recipe::class)->findBy([
                'id' => $neededRecipesId
            ]);

            $summedRecipes = $this->getDoctrine()
                ->getRepository(RecipeIngredient::class)
                ->findSum($neededRecipesId);

            return $this->render('random_recipe/index.html.twig', [
                'recipes' => $recipes,
                'recipeNames' => $names,
                'summedRecipes' => $summedRecipes
            ]);
        } else {
            $this->addFlash('success', $translator->trans('flash.failure'));
            return $this->redirectToRoute('home');
        }
    }
}
