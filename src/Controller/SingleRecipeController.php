<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class SingleRecipeController extends AbstractController
{
    /**
     * @Route("/single/recipe", name="single_recipe")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $name = $request->query->get('name');
        $imageUrl = $request->query->get('imageUrl');
        $ingredients = $request->query->get('ingredients');
        $instructions = $request->query->get('instructions');

        return $this->render('single_recipe/index.html.twig', [
            'name' => $name,
            'imageUrl' => $imageUrl,
            'ingredients' => $ingredients,
            'instructions' => $instructions,
        ]);
    }
}
