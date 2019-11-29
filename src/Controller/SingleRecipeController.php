<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
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

        $objects= $this->getDoctrine()->getRepository(RecipeIngredient::class)->findBy([
            'recipe' => $recipe
        ]);

        $ingredients =[];
        foreach ($objects as $item) {
            $temp = new Ingredient();
            $temp->name = $item->getIngredient()->getTitle();
            $temp->quantity = $item->getAmount() . $item->getMeasure()->getTitle();
            array_push($ingredients, $temp);
        }

        return $this->render('single_recipe/index.html.twig', [
                'recipe' => $recipe,
                'ingredients' => $ingredients
            ]);
    }
}
