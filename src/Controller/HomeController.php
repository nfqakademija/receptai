<?php

namespace App\Controller;

use App\Entity\Recipe;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    const RECIPES_PER_PAGE = 12;
    /**
     * @Route("/home", name="home")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $recipes = $this->getDoctrine()->getRepository(Recipe::class)->findAll();

        $pagination = $paginator->paginate(
            $recipes,
            $request->query->getInt('page', 1),
            self::RECIPES_PER_PAGE
        );

        return $this->render('home/index.html.twig', [
            'recipes' => $pagination,
        ]);
    }
}
