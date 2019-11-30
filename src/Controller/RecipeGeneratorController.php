<?php

namespace App\Controller;

use App\Entity\RecipeIngredient;
use App\Form\RecipeGeneratorType;
use App\Service\RecipesGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecipeGeneratorController extends AbstractController
{
    /**
     * @Route("/recipe/generator", name="recipe_generator", )
     */
    public function index(Request $request)
    {

        $form = $this->createForm(RecipeGeneratorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedTags = $form['title']->getData();

                $this->container->get('session')->set('titles', $selectedTags);

                return $this->redirect($this->generateUrl(
                    'recipe_generator_generated'
                ));
            }


        return $this->render('recipe_generator/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/recipe/generator/generated", name="recipe_generator_generated", methods="GET")
     */
    public function generate(RecipesGenerator $generator)
    {
        $selectedTags = $this->container->get('session')->get('titles');

        $generatedRecipeId = $generator->getGeneratedRecipesId($selectedTags);

        $selectedTagRecipes = $generator->getGeneratedRecipes($generatedRecipeId);

        $summedRecipes = $this->getDoctrine()
            ->getRepository(RecipeIngredient::class)
            ->findSum($generatedRecipeId);
        return $this->render('recipe_generator/generated.html.twig', [
            'selectedRecipes' => $selectedTagRecipes,
            'summedRecipes' => $summedRecipes,
        ]);
    }
}
