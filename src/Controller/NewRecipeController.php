<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Measure;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Form\IngredientType;
use App\Form\NewRecipeType;
use App\Form\TagType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;

class NewRecipeController extends AbstractController
{
    /**
     * @Route("/new/recipe", name="new_recipe")
     */
    public function new(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $recipe = new Recipe();

        $form = $this->createForm(NewRecipeType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageUrl */
            $imageUrl = $form['image']->getData();

            $recipe->setTitle($form['title']->getData());
            $recipe->setDescription($form['description']->getData());

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageUrl) {
                $originalFilename = pathinfo($imageUrl->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = transliterator_transliterate(
                    'Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()',
                    $originalFilename
                );
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageUrl->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageUrl->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $logger->critical('Image was not uploaded');
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $recipe->setImageUrl($newFilename);
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
}
