<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authUtils)
    {
        $error= $authUtils->getLastAuthenticationError();
        $lastUsername = $authUtils->getLastUsername();

        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        return $this->render('login/login.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername,
        ]);
    }
    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {

    }
}
