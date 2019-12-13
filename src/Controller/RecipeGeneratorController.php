<?php

namespace App\Controller;

use App\Entity\RecipeIngredient;
use App\Form\RecipeGeneratorType;
use App\Form\SaveType;
use App\Service\RecipesGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecipeGeneratorController extends AbstractController
{
    /**
     * @Route("/recipe/generator", name="recipe_generator", )
     */
    public function index(Request $request, RecipesGenerator $generator)
    {

        $form = $this->createForm(RecipeGeneratorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedTags = $form['title']->getData();

            $generatedRecipeIds = $generator->getGeneratedRecipesId($selectedTags);

            $this->container->get('session')->set('generatedRecipeIds', $generatedRecipeIds);

            return $this->redirect($this->generateUrl(
                'recipe_generator_generated'
            ));
        }

        return $this->render('recipe_generator/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/recipe/generator/generated", name="recipe_generator_generated")
     */
    public function generate(RecipesGenerator $generator, Request $request)
    {
        $recipesWereSaved = false;

        $form = $this->createForm(SaveType::class);
        $form->handleRequest($request);

        $generatedRecipeIds = $this->container->get('session')->get('generatedRecipeIds');

        if($generatedRecipeIds == null) {
            return $this->redirectToRoute('recipe_generator');
        }

        $selectedTagRecipes = $generator->getGeneratedRecipes($generatedRecipeIds);

        $summedRecipes = $this->getDoctrine()
            ->getRepository(RecipeIngredient::class)
            ->findSum($generatedRecipeIds);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $user->setRecipeIds($generatedRecipeIds);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();
            $recipesWereSaved = true;
        }

        foreach($generatedRecipeIds as $generatedRecipeId){
            echo $generatedRecipeId . ' ';
        }

        return $this->render('recipe_generator/generated.html.twig', [
            'selectedRecipes' => $selectedTagRecipes,
            'summedRecipes' => $summedRecipes,
            'saveForm' => $form->createView(),
            'recipesWereSaved' => $recipesWereSaved
        ]);
    }
}
