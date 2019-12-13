<?php

namespace App\Controller;

use App\Entity\Tag;
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
        $recipe = $this->getDoctrine()->getRepository(Recipe::class)
            ->find($id);
        $tags = $recipe->getTags();
        $tagObject = $this->getDoctrine()->getRepository(Tag::class)
            ->find($tags[0]);
        $recipes = $tagObject->getRecipes();
        $random = random_int(0, sizeof($recipes) - 1);

        return $this->render('card/index.html.twig', [
            'imageUrl' => $recipes[$random]->getImageUrl(),
            'name' => $recipes[$random]->getTitle(),
            'id' => $recipes[$random]->getId(),
        ]);
    }
}
