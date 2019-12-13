<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Form\NewRecipeType;
use App\Service\UploaderHelper;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class NewRecipeController extends AbstractController
{
    /**
     * @Route("/new/recipe", name="new_recipe")
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param LoggerInterface $logger
     * @param UploaderHelper $uploaderHelper
     * @param TranslatorInterface $translator
     * @return RedirectResponse|Response
     */
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        UploaderHelper $uploaderHelper,
        TranslatorInterface $translator
    ) {
        if ($this->getUser()) {
            $recipe = new Recipe();

            $user = $this->getUser();

            $form = $this->createForm(NewRecipeType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $imageUrl */
                $imageUrl = $form['image']->getData();

                $recipe->setTitle(ucfirst($form['title']->getData()));
                $recipe->setDescription(ucfirst($form['description']->getData()));

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
                    $ingredient->setTitle(ucfirst($title));

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
                $this->addFlash('success', $translator->trans('flash.newrecipe_success'));


                return $this->redirect($this->generateUrl('home'));
            }

            return $this->render('new_recipe/index.html.twig', [
                'form' => $form->createView(),
            ]);
        }
        $this->addFlash('danger', $translator->trans('flash.newrecipe_failure'));

        return $this->redirectToRoute('login');
    }

    /**
     * @Route("/recipe/edit/{id}", name="edit_recipe")
     * @param Request $request
     * @param $id
     * @param UploaderHelper $uploaderHelper
     * @param LoggerInterface $logger
     * @param TranslatorInterface $translator
     * @return RedirectResponse|Response
     */
    public function edit(
        Request $request,
        $id,
        UploaderHelper $uploaderHelper,
        LoggerInterface $logger,
        TranslatorInterface $translator
    ) {
        if ($this->getUser() && $this->getUser()->getRecipes()->contains($this->getDoctrine()->
            getRepository(Recipe::class)->find($id))
        ) {
            $entityManager = $this->getDoctrine()->getManager();
            $recipe = $entityManager->getRepository(Recipe::class)->find($id);

            $recipeIngredients = $recipe->getRecipeIngredients();

            $data = [];
            $measures = [];
            foreach ($recipeIngredients as $ri) {
                $tempData = [
                    'title' => $ri->getIngredient()->getTitle(),
                    'amount' => $ri->getAmount(),
                ];
                array_push($data, $tempData);
                array_push($measures, $ri->getMeasure());
            }

            $form = $this->createForm(NewRecipeType::class, $recipe);

            $tags = $recipe->getTags();

            $form['ingredients']->setData($data);

            foreach ($form['ingredients'] as $key => $ingredientForm) {
                $ingredientForm['title']->setData($recipeIngredients[$key]->getIngredient()->getTitle());
                $ingredientForm['amount']->setData($recipeIngredients[$key]->getAmount());
            }

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

                foreach ($tags as $tag) {
                    $recipe->removeTag($tag);
                }

                foreach ($form['tags'] as $tagForm) {
                    $tag = $tagForm['title']->getData();
                    $recipe->addTag($tag);
                }

                foreach ($recipeIngredients as $ri) {
                    $entityManager->remove($ri);
                }

                foreach ($form['ingredients'] as $ingredientForm) {
                    $title = $ingredientForm['title']->getData();
                    $measure = $ingredientForm['measure']->getData();
                    $amount = $ingredientForm['amount']->getData();

                    $persistedIngredient = $entityManager->getRepository(Ingredient::class)
                        ->findOneBy(['title' => $title]);
                    if ($persistedIngredient == null) {
                        $persistedIngredient = new Ingredient();
                        $persistedIngredient->setTitle($title);
                        $entityManager->persist($persistedIngredient);
                    }

                    $recipeIngredient = new RecipeIngredient();
                    $recipeIngredient->setRecipe($recipe);
                    $recipeIngredient->setIngredient($persistedIngredient);
                    $recipeIngredient->setMeasure($measure);

                    if ($amount != null) {
                        $recipeIngredient->setAmount($amount);
                    }

                    $entityManager->persist($recipeIngredient);
                }

                $entityManager->flush();
                $this->addFlash('success', $translator->trans('flash.editrecipe_success'));

                return $this->redirectToRoute('user_created_recipes');
            }

            return $this->render('new_recipe/edit.html.twig', [
                'form' => $form->createView(),
                'tags' => $tags,
                'measures' => $measures,
            ]);
        }
        $this->addFlash('danger', $translator->trans('flash.editrecipe_failure'));

        return $this->redirectToRoute('login');
    }
}
