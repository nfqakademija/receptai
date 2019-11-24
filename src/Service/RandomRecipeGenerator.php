<?php

namespace App\Service;

class RandomRecipeGenerator
{
    public function getRecipeIdArray(int $recipeCount): array
    {
        $recipeIdArray = array();

        for ($i = 1; $i <= $recipeCount; $i++) {
            $recipeIdArray[] = $i;
        }

        $neededRecipesId = array();
        for ($i = 0; $i < 7; $i++) {
            try {
                $randomRecipeArrayId = random_int(0, $recipeCount - 1);
            } catch (\Exception $e) {
            }
            $randomRecipeDatabaseId = $randomRecipeArrayId+1;
            while (!in_array($randomRecipeDatabaseId, $recipeIdArray)) {
                try {
                    $randomRecipeArrayId = random_int(0, $recipeCount - 1);
                } catch (\Exception $e) {
                }
                $randomRecipeDatabaseId = $randomRecipeArrayId + 1;
            }
            if (in_array($randomRecipeDatabaseId, $recipeIdArray)) {
                $neededRecipesId[] = $randomRecipeDatabaseId;
                $key = array_search($randomRecipeDatabaseId, $recipeIdArray);
                unset($recipeIdArray[$key]);
                $recipeIdArray = array_values($recipeIdArray);
            }
        }
        return $neededRecipesId;
    }
}
