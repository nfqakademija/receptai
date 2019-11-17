<?php

namespace App\Service;

class RandomRecipeGenerator
{
    function getRecipeIdArray(int $totalRecipes, array $recipeIdArray): array
    {
        $neededRecipesId = array();
        for ($i = 0 ; $i < 7 ; $i++) {
            $randomm = random_int(0, $totalRecipes - 1);
            $randomas = $randomm++;
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
        return $neededRecipesId;
    }
}