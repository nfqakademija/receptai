<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Form\NewRecipeType;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NewRecipeController extends AbstractController
{
    /**
     * @Route("/new/recipe", name="new_recipe")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param UploaderHelper $uploaderHelper
     * @return RedirectResponse|Response
     */
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        UploaderHelper $uploaderHelper
    ) {
        if ($this->getUser()) {
            $recipe = new Recipe();

            $user = $this->getUser();

            $form = $this->createForm(NewRecipeType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $imageUrl */
                $imageUrl = $form['image']->getData();

                $recipe->setTitle($form['title']->getData());
                $recipe->setDescription($form['description']->getData());
                $recipe->setCreatedUser($user);

                if ($imageUrl) {
                    $imageFileName = $uploaderHelper->upload($imageUrl, $logger);
                    $recipe->setImageUrl($imageFileName);
                }

                foreach ($form['tags'] as $tagForm) {
                    $tag = $tagForm['title']->getData();
                    $recipe->addTag($tag);
                }

                $entityManager->persist($recipe);

                foreach ($form['ingredients'] as $ingredientForm) {
                    $title = $ingredientForm['title']->getData();
                    $measure = $ingredientForm['measure']->getData();
                    $amount = $ingredientForm['amount']->getData();

                    $ingredient = new Ingredient();
                    $ingredient->setTitle($title);

                    $recipeIngredient = new RecipeIngredient();
                    $recipeIngredient->setRecipe($recipe);
                    $recipeIngredient->setIngredient($ingredient);
                    $recipeIngredient->setMeasure($measure);

                    if ($amount != null) {
                        $recipeIngredient->setAmount($amount);
                    }

                    $entityManager->persist($ingredient);
                    $entityManager->persist($recipeIngredient);
                }

                $entityManager->flush();

                return $this->redirect($this->generateUrl('home'));
            }

            return $this->render('new_recipe/index.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/recipe/edit/{id}", name="edit_recipe")
     * @param Request $request
     * @param $id
     * @param UploaderHelper $uploaderHelper
     * @param LoggerInterface $logger
     * @return RedirectResponse|Response
     */
    public function edit(Request $request, $id, UploaderHelper $uploaderHelper, LoggerInterface $logger)
    {
        if ($this->getUser()) {
            $entityManager = $this->getDoctrine()->getManager();
            $recipe = $entityManager->getRepository(Recipe::class)->find($id);
            $tags = $recipe->getTags();
            $recipeIngredients = $entityManager->getRepository(RecipeIngredient::class)
                ->findBy([
                    'recipe' => $recipe
                ]);

            $form = $this->createForm(NewRecipeType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $imageUrl */
                $imageUrl = $form['image']->getData();

                $recipe->setTitle($form['title']->getData());
                $recipe->setDescription($form['description']->getData());

                if ($imageUrl) {
                    $imageFileName = $uploaderHelper->upload($imageUrl, $logger);
                    $recipe->setImageUrl($imageFileName);
                }

                foreach ($form['tags'] as $tagForm) {
                    $tag = $tagForm['title']->getData();
                    $recipe->addTag($tag);
                }

                foreach ($form['ingredients'] as $ingredientForm) {
                    $title = $ingredientForm['title']->getData();
                    $measure = $ingredientForm['measure']->getData();
                    $amount = $ingredientForm['amount']->getData();

                    $ingredient = new Ingredient();
                    $ingredient->setTitle($title);

                    $recipeIngredient = new RecipeIngredient();
                    $recipeIngredient->setRecipe($recipe);
                    $recipeIngredient->setIngredient($ingredient);
                    $recipeIngredient->setMeasure($measure);

                    if ($amount != null) {
                        $recipeIngredient->setAmount($amount);
                    }
                }

                $entityManager->flush();

                return $this->redirect($this->generateUrl('user_created_recipes'));
            }

            return $this->render('new_recipe/edit.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        return $this->redirectToRoute('login');
    }
}
