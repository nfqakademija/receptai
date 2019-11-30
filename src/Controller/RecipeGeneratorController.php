<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Form\RecipeGeneratorType;
use App\Service\RecipeGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class RecipeGeneratorController extends AbstractController
{
    /**
     * @Route("/recipe/generator", name="recipe_generator", )
     */
    public function index(Request $request, TranslatorInterface $translator)
    {

        $form = $this->createForm(RecipeGeneratorType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedTags = $form['title']->getData();
            if (count($selectedTags) == 0) {
                $this->addFlash('warning', $translator->trans('Please select a tag before continuing'));
                return $this->redirectToRoute('recipe_generator');
            } else {
                $this->container->get('session')->set('titles', $selectedTags);

                return $this->redirect($this->generateUrl(
                    'recipe_generator_generated'
                ));


                //sql where tags implodintas lygus nors vienas
                //if tags lygu titles of tags
                //Daryt needed recipes id, dabar prasukti cikla pro allrecipes, tada per tagus, jei nors vienas tagas atitinka
                //Prideti to recepto id i needed receptu id array ir tada findby id recipe padaryt
            }
        }

        return $this->render('recipe_generator/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/recipe/generator/generated", name="recipe_generator_generated", methods="GET")
     */
    public function generate( Request $request)
    {
        $selectedTags = $this->container->get('session')->get('titles');




        $neededRecipeId = $this->getDoctrine()
            ->getRepository(Recipe::class)
            ->getNeededId($selectedTags);
       // var_dump($neededRecipeId);
      //  echo $neededRecipeId;

        foreach ($neededRecipeId as $needed){
            echo $needed . ' ++++ ';
        }

        if(count($neededRecipeId) < 7){
            $remainingRecipeId = $this->getDoctrine()
                ->getRepository(Recipe::class)
                ->getRemainingRecipeId($neededRecipeId, 7 - count($neededRecipeId));
            echo count($remainingRecipeId) . '++';
            foreach ($remainingRecipeId as $needed){
                echo $needed . ' ---- ';
            }
            $neededRecipeId = array_merge($neededRecipeId, $remainingRecipeId);

           // foreach ($neededRecipeId as $needed){
           //     echo $needed . ' ++++ ';
          //  }
            echo ' ++++++++++ ';
            echo count($neededRecipeId);
        }
        echo count($neededRecipeId);



        //Sucombinint abu array ir tiesiog rand padaryt juos

      //  echo count($neededRecipeId);
      //  $neededRecipeId = $recipeGenerator->selectedTagsRecipesId($allRecipes, $selectedTags);
        //Move to services
        //$randomizedRecipeId = $recipeGenerator->randomizeSelectedTagsRecipesId($neededRecipeId);
        // $selectedTagRecipes
        //Pradziai gauna tiesiog visus atitinkancius. For ciklas. Random skaiciu is id array 7 kart.
        // Issaugoji ta id i kita array Darai counta,
        //Jei needed recipes id atitinka darai counta ir is array ismeti ta id - value, kuri buvo pasirinkta
        //Jei neededRecipesId count nera daugiau ar lygu septyniem tai for ciklas eina iki counto
        //Likusieji buna null ar kazkas panasiai, kad nebeturime receptu daugiau tokiu
        //Pridet buttona, kuris klausia ar norite kitokiu receptu su tais paciais tagais,
        // tai tiesiog refreshina puslapi
        //Vel daryt recipe_tag
        $selectedTagRecipes = $this->getDoctrine()->getRepository(Recipe::class)->findBy([
            'id' => $neededRecipeId
        ]);
        shuffle($selectedTagRecipes);
        $summedRecipes = $this->getDoctrine()
            ->getRepository(RecipeIngredient::class)
            ->findSum($neededRecipeId);
        return $this->render('recipe_generator/generated.html.twig', [
            'selectedRecipes' => $selectedTagRecipes,

            'summedRecipes' => $summedRecipes,
        ]);
    }
}
