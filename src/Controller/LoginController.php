<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

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

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(UrlGeneratorInterface $urlGenerator, UserInterface $user = null)
    {
        if ($user instanceof User) {
            return $this->render('security/profile.html.twig', [
                'user' => $user,
                'userId' => $user->getId(),
                'roles' => $this->roleNames($user->getRoles()),
            ]);
        }
        // Redirect for not logged in users (or different kind)
        return new RedirectResponse($urlGenerator->generate('login'));
    }
    private function roleNames(array $userRoles)
    {
        foreach ($userRoles as $role) {
            yield str_replace('ROLE_', '', $role);
        }
    }
}

