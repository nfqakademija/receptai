<?php

namespace App\Controller;

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
    public function index(int $id)
    {
        $generatedRecipeIds = $this->container->get('session')->get('generatedRecipeIds');

        $key = array_search($id, $generatedRecipeIds);
  ;
        $recipe = $this->getDoctrine()->getRepository(Recipe::class)
            ->find($id);

        $tags = $recipe->getTags();

        $rerolledRecipeId = $this->getDoctrine()
            ->getRepository(Recipe::class)
            ->getRerolledAJaxId($tags, $generatedRecipeIds);

        $ids = array_shift($rerolledRecipeId);

        $neededRecipe = $this->getDoctrine()->getRepository(Recipe::class)->find($ids);

        $generatedRecipeIds[$key] = $ids;

        $this->container->get('session')->set('generatedRecipeIds', $generatedRecipeIds);

        return $this->render('card/index.html.twig', [
            'imageUrl' => $neededRecipe->getImageUrl(),
            'name' => $neededRecipe->getTitle(),
            'id' => $neededRecipe->getId(),
        ]);
    }
}
