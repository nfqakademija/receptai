<?php

namespace App\Controller;

use App\Entity\RecipeIngredient;
use App\Service\RecipesGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class SavedRecipeController extends AbstractController
{
    /**
     * @Route("/saved/recipe", name="saved_recipe")
     */
    public function index(RecipesGenerator $generator, TranslatorInterface $translator)
    {
        if ($this->getUser()) {
            $user = $this->getUser();

            if ($user->getRecipeIds() == null) {
                $this->addFlash('danger', $translator->trans('flash.saveFirst'));
                return $this->redirectToRoute('recipe_generator');
            }
            $savedRecipeIds = json_decode(json_encode($user->getRecipeIds()), true);

            $selectedTagRecipes = $generator->getGeneratedRecipes($savedRecipeIds);

            $summedRecipes = $this->getDoctrine()
                ->getRepository(RecipeIngredient::class)
                ->findSum($savedRecipeIds);

            return $this->render('saved_recipe/index.html.twig', [
                'selectedRecipes' => $selectedTagRecipes,
                'summedRecipes' => $summedRecipes,
            ]);
        }
        return $this->redirectToRoute('login');
    }
}
