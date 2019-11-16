<?php

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Routing\Annotation\Route;

class RandomRecipeController extends AbstractController
{
    /**
     * @Route("/random/recipe", name="random_recipe")
     */
    public function random()
    {
        $em = $this->getDoctrine()->getManager();
        $recipes = $em->getRepository(Recipe::class);

        //Sita funkcija galima accesint tik jei prisijunges
        //Dar reikes padaryt, kad kiekvienam prisijungusiam useriui butu skirtinga
        //Kai sugeneratina random inta nuo 1 - visa rows count
        //Padaryt kad nueitu i kita functiona, kuriame iskviestu ta id
        //Bet jei id jau buvo nusiustas kazkaip padaryt, kad to neimtu
        //Random reiketu idet i ciklo vidu
        //Padaryt for cikla nuo 1 iki counto
        //Sudet i array skaicius nuo 1 iki receptu galo
        //Kita for cikla kuriame lygini id , jei netinka id duoda kita random, arba while padaryt kol nesuranda random
        //Jo while padaryt kol nera naujas id, if array. count = 0, tada baigias
        //For cikla, kuris eina iki 7 ir jame yra while ciklas
        //Padaryt, kad tas chro job eitu kas minute pradziai pabandyt
        //Perkelt recepto pavadinima i $variable, kad lengviau twige butu viska daryt
        //Padaryt, kad issiustu recepto pavadinima i pasta su ingredientais
        //Jei ingredientas tas pats sudet amount, padaryti double array su ingredientu ir jo kiekiu kad galetum sudet kieki
        //Cikla, kuris ideda jei jau neegzistuoja ingredientas ir kitas ciklas, kuris prideda amount
        $totalRecipes = $recipes->createQueryBuilder('a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();

        $recipeIdArray = array();

        for ($i = 1; $i <= $totalRecipes; $i++) {
            $recipeIdArray[] = $i;
        }

        //Po to pakeist i 7, kai bus ne 3 receptai tik
        /*  for ($i = 0; $i < 4; $i++) {
              $random = random_int(0, count($recipeIdArray));
              //for ($j = 0; $j < count($recipeIdArray); $j++) {
              unset($recipeIdArray[$random]);
          } */
        $random = random_int(1, $totalRecipes);


        $random = random_int(0, $totalRecipes);

        $neededRecipesId = array();
        //7 Dienom suranda po viena recepta
        //Reikes perkelt dar, i kita funkcija, nes reikia, kad kiekvienam useriui skirtingai rodytu
        //Ir issavintu
        for ($i = 0 ; $i < 7 ; $i++) {
            $randomm = random_int(0, $totalRecipes - 1);
            $randomas = $randomm++;
            //Daryti recipe id array
            while (!in_array($randomas, $recipeIdArray)) {
                $randomm = random_int(0, $totalRecipes - 1);
                $randomas = $randomm + 1;

            }
            if (in_array($randomas, $recipeIdArray)) {
                $neededRecipesId[] = $randomas;
                $key = array_search($randomas, $recipeIdArray);
                unset($recipeIdArray[$key]);
                $recipeIdArray = array_values($recipeIdArray);
            }
        }
        $posts = $this->getDoctrine()->getRepository(RecipeIngredient::class)->findBy([
            'recipe' => $neededRecipesId
        ]);





     //   $randomas = random_int(0, count($));
      //  array_splice($recipeIdArray,$randomas,1);
     //   $recipeIdArray = array_values($recipeIdArray);
     //   $randomas = random_int(0, 3);
      //  array_splice($recipeIdArray,$randomas,1);
     //   $recipeIdArray = array_values($recipeIdArray);


      // $random =  count($recipeIdArray);
        $name = '';
        //Visas receptas su ingredientais vienoj pusej ir desinej pusej visi ingredientai, kuriu reikes, kad pagaminti
       // foreach ($posts as $post){
       // }



        dump($posts);

        return $this->render('random_recipe/index.html.twig', [
            'total' => $totalRecipes,
           'posts' => $posts,
            'randomNr' => $randomas,
            'recipes' => $neededRecipesId,
        ]);
    }
}
# send to the logged in user the random recipe