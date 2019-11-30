<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Form\RecipeGeneratorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RecipeGeneratorController extends AbstractController
{
    /**
     * @Route("/recipe/generator", name="recipe_generator", )
     */
    public function index(Request $request, TranslatorInterface $translator)
    {

        $form = $this->createForm(RecipeGeneratorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedTags = $form['title']->getData();
            if (count($selectedTags) == 0) {
                $this->addFlash('warning', $translator->trans('flash.select'));
                return $this->redirectToRoute('recipe_generator');
            } else {
                $this->container->get('session')->set('titles', $selectedTags);

                return $this->redirect($this->generateUrl(
                    'recipe_generator_generated'
                ));
            }
        }

        return $this->render('recipe_generator/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/recipe/generator/generated", name="recipe_generator_generated", methods="GET")
     */
    public function generate()
    {
        $selectedTags = $this->container->get('session')->get('titles');

        $generatedRecipeId = $this->getDoctrine()
            ->getRepository(Recipe::class)
            ->getNeededId($selectedTags);

        if (count($generatedRecipeId) < 7) {
            $remainingRecipeId = $this->getDoctrine()
                ->getRepository(Recipe::class)
                ->getRemainingRecipeId($generatedRecipeId, 7 - count($generatedRecipeId));
            $generatedRecipeId = array_merge($generatedRecipeId, $remainingRecipeId);
        }

        $selectedTagRecipes = $this->getDoctrine()->getRepository(Recipe::class)->findBy([
            'id' => $generatedRecipeId
        ]);
        shuffle($selectedTagRecipes);
        $summedRecipes = $this->getDoctrine()
            ->getRepository(RecipeIngredient::class)
            ->findSum($generatedRecipeId);
        return $this->render('recipe_generator/generated.html.twig', [
            'selectedRecipes' => $selectedTagRecipes,
            'summedRecipes' => $summedRecipes,
        ]);
    }
}
