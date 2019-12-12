<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AjaxController extends AbstractController
{
    /**
     * @Route("/ajax", name="ajax")
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $id = $request->get('id');

        
        return $this->render('card/index.html.twig', [
            'imageUrl' => "something",
            'name' => "chicken magic",
            'id' => 2,
        ]);
    }
}
