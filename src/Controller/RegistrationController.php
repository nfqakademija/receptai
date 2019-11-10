<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/registration", name="registration")
     */
    public function registration(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder,
        ValidatorInterface $validator
    ) {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);
        $form->handleRequest($request);

        if ($this->getUser()){
            return $this->redirectToRoute('home');
        }

        $errors = $validator->validate($user);

        if ($form->isSubmitted() && !$form->isValid()) {
            return $this->render('registration/registration.html.twig', [
                'our_form' => $form->createView(),
                'errors' => $errors,
            ]);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $passwordEncoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Registered successfully');
            return $this->redirectToRoute('registration');
        }

        return $this->render('registration/registration.html.twig', [
            'our_form' => $form->createView(),
            'errors' => $errors,
        ]);
    }
}
