<?php

namespace App\Controller;

use App\Entity\Recipe;
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
        $id = $request->query->get('id');
        $recipe = $this->getDoctrine()->getRepository(Recipe::class)->find($id);

        return $this->render('single_recipe/index.html.twig', [
            'recipe' => $recipe
        ]);
    }
}
