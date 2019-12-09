<?php

namespace App\Controller;

use App\Entity\Recipe;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UserCreatedRecipesController extends AbstractController
{
    /**
     * @Route("/user/created/recipes", name="user_created_recipes")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $user = $this->getUser();
        $recipes = $this->getDoctrine()->getRepository(Recipe::class)
            ->findBy([
                'created_user' => $user
            ]);

        $pagination = $paginator->paginate(
            $recipes,
            $request->query->getInt('page', 1),
            4
        );

        return $this->render('user_created_recipes/index.html.twig', [
            'recipes' => $pagination,
        ]);
    }
}
