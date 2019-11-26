<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\RecipeGeneratorType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RecipeGeneratorController extends AbstractController
{
    /**
     * @Route("/recipe/generator", name="recipe_generator")
     */
    public function index(Request $request)
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();
        //$em = $this->getDoctrine()->getManager();
        $form = $this->createForm(RecipeGeneratorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Go to other controller
            $titles = $form['title']->getData();
            foreach ($titles as $title){
                echo $title;
            }

        }

        return $this->render('recipe_generator/index.html.twig', [
            'form' => $form->createView(),
            'tags' => $tags,
        ]);
    }
}
