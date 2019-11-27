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
     * @Route("/recipe/{id}", name="single_recipe", requirements={"id"="\d+"})
     * @param int $id
     * @return Response
     */
    public function index(int $id)
    {
        $recipe = $this->getDoctrine()->getRepository(Recipe::class)->find($id);
        if ($recipe === null) {
            throw new NotFoundHttpException("Page not found");
        }
        return $this->render('single_recipe/index.html.twig', [
                'recipe' => $recipe
            ]);
    }
}
