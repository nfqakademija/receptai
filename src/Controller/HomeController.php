<?php

namespace App\Controller;

use App\Entity\Recipe;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        $recipes = $this->getDoctrine()->getRepository(Recipe::class)->findAll();

        $pagination = $paginator->paginate(
            $recipes,
            $request->query->getInt('page', 1),
            2
        );

        return $this->render('home/index.html.twig', [
            'recipes' => $pagination,
        ]);
    }
}
