<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Entity\Tag;
use App\Form\RecipeGeneratorType;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RecipeGeneratorController extends AbstractController
{
    /**
     * @Route("/recipe/generator", name="recipe_generator")
     */
    public function index(Request $request, TranslatorInterface $translator)
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
                $recipes = $this->getDoctrine()->getRepository(Tag::class)->findBy([
                    'title' => $titles
                ]);
                $titles = iterator_to_array($titles);

                //   $goodRecipes = $this->getDoctrine()
                //       ->getRepository(Recipe::class)
                //     ->findRecipesWithTags($titles);

                $allRecipes = $this->getDoctrine()->getRepository(Recipe::class)->findAll();

                //Move to services
                $neededRecipeId = array();

                foreach ($titles as $title) {
                    foreach ($allRecipes as $recipe) {
                        echo $recipe . '+';
                        foreach ($recipe->getTags() as $tag) {
                            if ($title == $tag) {
                                $neededRecipeId[] = $recipe->getId();
                                break;
                            }
                            echo $tag . ' --- ';
                        }
                    }
                }
                echo "       ";
                foreach ($neededRecipeId as $recipeId) {
                    echo $recipeId . ' ';
                }
                echo "    ++++++++   ";
                $neededRecipeId = array_unique($neededRecipeId);
                foreach ($neededRecipeId as $recipeId) {
                    echo $recipeId . ' ';
                }


                $value = $neededRecipeId[array_rand($neededRecipeId)];
                echo '+++++++++++'. $value . '++++++++++++++';

                $randomRecipeId = array();
                //Darom cikla, kuris runnina per 7kart, nes 7 dienos, if neededRecipesId count = 0 break;
                for ($i=0; $i<7; $i++) {
                    $randomValue = $neededRecipeId[array_rand($neededRecipeId)];
                    $randomRecipeId[] = $randomValue;
                    $key = array_search($randomValue, $neededRecipeId);
                    unset($neededRecipeId[$key]);
                    echo count($neededRecipeId);
                    if(count($neededRecipeId) == 0){
                        break;
                    }
                }
                echo '--------------+++++++++++++++++++--------------';
                foreach ($randomRecipeId as $needed){
                    echo $needed . ' ';
                }


                // $selectedTagRecipes
                //Pradziai gauna tiesiog visus atitinkancius. For ciklas. Random skaiciu is id array 7 kart.
                // Issaugoji ta id i kita array Darai counta,
                //Jei needed recipes id atitinka darai counta ir is array ismeti ta id - value, kuri buvo pasirinkta
                //Jei neededRecipesId count nera daugiau ar lygu septyniem tai for ciklas eina iki counto
                //Likusieji buna null ar kazkas panasiai, kad nebeturime receptu daugiau tokiu
                //Pridet buttona, kuris klausia ar norite kitokiu receptu su tais paciais tagais,
                // tai tiesiog refreshina puslapi

                //I twiga paduot counta ir padaryk kad for ciklas eitu per counta ir tada jei countas nera 7,
                // tai tada i recipe paduot kad nebera daugiau receptu
                //Perduot counta ir 7-countas kiek receptu neturime;
                $selectedTagRecipes = $this->getDoctrine()->getRepository(Recipe::class)->findBy([
                    'id' => $randomRecipeId
                ]);
                $recipeCount = 7 - count($randomRecipeId) ;
                echo '+++++++++++++++++++++++++++++++++++++++++++++';
                echo $recipeCount;

                $summedRecipes = $this->getDoctrine()
                    ->getRepository(RecipeIngredient::class)
                    ->findSum($randomRecipeId);

                return $this->render('recipe_generator/generated.html.twig', [
                    'selectedRecipes' => $selectedTagRecipes,
                    'recipeCount' => $recipeCount,
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
