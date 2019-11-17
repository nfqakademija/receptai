<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Recipe;
use App\Entity\RecipeIngredient;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\RandomRecipeGenerator;
use Symfony\Contracts\Translation\TranslatorInterface;

class RandomRecipeController extends AbstractController
{
    //Is recipe lenteles imt ir tada is, kartojasi recep
    /**
     * @Route("/random/recipe", name="random_recipe")
     */
    public function random(RandomRecipeGenerator $recipeGenerator, TranslatorInterface $translator)
    {
        //Su buttonu generate padaryt, kai paspaudi padaryt, kad galetum kita kart spaust po valandos
        if ($this->getUser()) {
            $em = $this->getDoctrine()->getManager();
            $recipes = $em->getRepository(Recipe::class);
            $ingredients = $em->getRepository(RecipeIngredient::class);

            $products = $this->getDoctrine()
                ->getRepository(Recipe::class);
            //Move to repository
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
            $query = $ingredients->createQueryBuilder('s')
                ->addSelect('SUM(s.amount) as total')
                ->where('s.recipe IN (:neededRecipesId)')
                ->setParameter('neededRecipesId', $neededRecipesId)
                ->addSelect('s')
                ->groupBy('s.ingredient')
                ->getQuery();
            $soins = $query->getResult();

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
        //Save state controller
    }
}
# send to the logged in user the random recipe