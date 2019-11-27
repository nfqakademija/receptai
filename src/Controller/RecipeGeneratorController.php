<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\Tag;
use App\Form\RecipeGeneratorType;
use App\Repository\RecipeRepository;
use App\Service\RecipeGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RecipeGeneratorController extends AbstractController
{
    /**
     * @Route("/recipe/generator", name="recipe_generator")
     */
    public function index(Request $request, TranslatorInterface $translator, RecipeGenerator $recipeGenerator)
    {
        $tags = $this->getDoctrine()->getRepository(Tag::class)->findAll();
        //$em = $this->getDoctrine()->getManager();
        $form = $this->createForm(RecipeGeneratorType::class);


        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Go to other controller
            $titles = $form['title']->getData();
            if (count($titles) == 0) {
                $this->addFlash('warning', $translator->trans('Please select a tag before continuing'));
                return $this->redirectToRoute('recipe_generator');
                //flash message return to same page and say please select a tag before continuing
            } else {
                //sql where tags implodintas lygus nors vienas
                //if tags lygu titles of tags
                //Daryt needed recipes id, dabar prasukti cikla pro allrecipes, tada per tagus, jei nors vienas tagas atitinka
                //Prideti to recepto id i needed receptu id array ir tada findby id recipe padaryt


                //   $goodRecipes = $this->getDoctrine()
                //       ->getRepository(Recipe::class)
                //     ->findRecipesWithTags($titles);

                $allRecipes = $this->getDoctrine()->getRepository(Recipe::class)->findAll();

                //Move to services
                $neededRecipeId = $recipeGenerator->selectedTagsRecipesId($allRecipes, $titles);

                $randomizedRecipeId = $recipeGenerator->randomizeSelectedTagsRecipesId($neededRecipeId);




                // $selectedTagRecipes
                //Pradziai gauna tiesiog visus atitinkancius. For ciklas. Random skaiciu is id array 7 kart.
                // Issaugoji ta id i kita array Darai counta,
                //Jei needed recipes id atitinka darai counta ir is array ismeti ta id - value, kuri buvo pasirinkta
                //Jei neededRecipesId count nera daugiau ar lygu septyniem tai for ciklas eina iki counto
                //Likusieji buna null ar kazkas panasiai, kad nebeturime receptu daugiau tokiu
                //Pridet buttona, kuris klausia ar norite kitokiu receptu su tais paciais tagais,
                // tai tiesiog refreshina puslapi

                $selectedTagRecipes = $this->getDoctrine()->getRepository(Recipe::class)->findBy([
                    'id' => $randomizedRecipeId
                ]);

                $summedRecipes = $this->getDoctrine()
                    ->getRepository(RecipeIngredient::class)
                    ->findSum($randomizedRecipeId);

                return $this->render('recipe_generator/generated.html.twig', [
                    'selectedRecipes' => $selectedTagRecipes,
                    'recipeCount' => 7 - count($randomizedRecipeId),
                    'summedRecipes' => $summedRecipes,
                ]);
            }
        }

        return $this->render('recipe_generator/index.html.twig', [
            'form' => $form->createView(),
            'tags' => $tags,

        ]);
    }
}
