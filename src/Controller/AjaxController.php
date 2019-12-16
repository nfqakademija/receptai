<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use App\Service\RerolledRecipeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Recipe;

class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax/{id}", name="ajax", requirements={"id"="\d+"})
     * @param int $id
     * @return Response
     */
    public function index(int $id, RecipeRepository $recipeRepository, RerolledRecipeService $recipeService)
    {
        $generatedRecipeIds = $this->container->get('session')->get('generatedRecipeIds');
        $selectedTags = $this->container->get('session')->get('selectedTags');
        $key = array_search($id, $generatedRecipeIds);

        $recipe = $recipeRepository->find($id);

        $tags = $recipe->getTags();

        $rerolledId = $recipeService->getNeededRerollId($selectedTags, $generatedRecipeIds, $tags);

        $neededRecipe = $this->getDoctrine()->getRepository(Recipe::class)->find($rerolledId);

        $generatedRecipeIds[$key] = $rerolledId;

        $this->container->get('session')->set('generatedRecipeIds', $generatedRecipeIds);

        return $this->render('card/index.html.twig', [
            'imageUrl' => $neededRecipe->getImageUrl(),
            'name' => $neededRecipe->getTitle(),
            'id' => $neededRecipe->getId(),
        ]);
    }
}
