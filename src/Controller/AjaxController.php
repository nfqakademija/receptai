<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

        return $this->render('card/index.html.twig', [
            'imageUrl' => "something",
            'name' => "hi",
            'id' => $id,
        ]);
    }
}
