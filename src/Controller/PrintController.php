<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class PrintController extends AbstractController
{
    /**
     * @Route("/print", name="print")
     */
    public function index()
    {
        return $this->render('print/index.html.twig', [
        ]);
    }
}
