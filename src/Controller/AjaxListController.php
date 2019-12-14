<?php

namespace App\Controller;

use App\Entity\RecipeIngredient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AjaxListController extends AbstractController
{
    /**
     * @Route("/ajax/list", name="ingredient_list")
     */
    public function index()
    {
        $generatedRecipeIds = $this->container->get('session')->get('generatedRecipeIds');
        $summedRecipes = $this->getDoctrine()
            ->getRepository(RecipeIngredient::class)
            ->findSum($generatedRecipeIds);

        return $this->render('ingredient_list/index.html.twig', [
            'ingredientList' => $summedRecipes,
        ]);
    }
}
