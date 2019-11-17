<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $recipes = $this->getDoctrine()->getRepository(RecipeIngredient::class)->findAll();
        $names = $this->getDoctrine()->getRepository(Recipe::class)->findAll();

        return $this->render('home/index.html.twig', [
            'recipes' => $recipes,
            'recipeNames' => $names
        ]);
    }
}
