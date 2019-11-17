<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RandomRecipeGenerator;
use Symfony\Contracts\Translation\TranslatorInterface;

class RandomRecipeController extends AbstractController
{
    /**
     * @Route("/random/recipe", name="random_recipe")
     */
    public function random(RandomRecipeGenerator $recipeGenerator, TranslatorInterface $translator)
    {

        if ($this->getUser()) {

            $em = $this->getDoctrine()->getManager();
            $recipes = $em->getRepository(Recipe::class);
            $ingredients = $em->getRepository(RecipeIngredient::class);

            //Sita funkcija galima accesint tik jei prisijunges
            //Dar reikes padaryt, kad kiekvienam prisijungusiam useriui butu skirtinga
            //Padaryt, kad tas chro job eitu kas minute pradziai pabandyt
            //Perkelt recepto pavadinima i $variable, kad lengviau twige butu viska daryt
            //Padaryt, kad issiustu recepto pavadinima i pasta su ingredientais
            //Jei ingredientas tas pats sudet amount, padaryti double array su ingredientu ir jo kiekiu kad galetum sudet kieki
            //Cikla, kuris ideda jei jau neegzistuoja ingredientas ir kitas ciklas, kuris prideda amount

            //Move to service
            $totalRecipes = $recipes->createQueryBuilder('a')
                ->select('count(a.id)')
                ->getQuery()
                ->getSingleScalarResult();

            $recipeIdArray = array();

            for ($i = 1; $i <= $totalRecipes; $i++) {
                $recipeIdArray[] = $i;
            }

            //7 Dienom suranda po viena recepta
            //Reikes perkelt dar, i kita funkcija, nes reikia, kad kiekvienam useriui skirtingai rodytu
            //Ir issavintu
            $neededRecipesId = $recipeGenerator->getRecipeIdArray($totalRecipes, $recipeIdArray);


            $posts = $this->getDoctrine()->getRepository(RecipeIngredient::class)->findBy([
                'recipe' => $neededRecipesId
            ]);
            $names = $this->getDoctrine()->getRepository(Recipe::class)->findBy([
                'id' => $neededRecipesId
            ]);
           // Daryt for cikla, kuris eina per
            //Move to repository
            $ids_string = implode(',', $neededRecipesId);
            $query = $ingredients->createQueryBuilder('s')
                ->addSelect('SUM(s.amount) as total')
                ->where('s.recipe IN (:neededRecipesId)')
                ->setParameter('neededRecipesId', $neededRecipesId)
                ->addSelect('s')
                ->groupBy('s.ingredient')
                ->getQuery();
            $soins = $query->getResult();

            dump($posts);

            return $this->render('random_recipe/index.html.twig', [
                'total' => $totalRecipes,
                'posts' => $posts,
                'recipes' => $names,
                'soins' => $soins
            ]);
        }
        else {
            $this->addFlash('failure', $translator->trans('Please log in'));
            return $this->redirectToRoute('home');
        }
    }
}
# send to the logged in user the random recipe