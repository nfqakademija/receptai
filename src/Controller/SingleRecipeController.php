<?php

namespace App\Controller;

use App\Entity\Recipe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class SingleRecipeController extends AbstractController
{
    /**
     * @Route("/single/recipe/{id}", name="single_recipe")
     * @param string $id
     * @return Response
     */
    public function index(string $id)
    {
        $recipe = $this->getDoctrine()->getRepository(Recipe::class)->find(intval($id));
        if ($recipe === null) {
            throw new NotFoundHttpException("Page not found");
        } else {
            return $this->render('single_recipe/index.html.twig', [
                'recipe' => $recipe
            ]);
        }
    }
}
