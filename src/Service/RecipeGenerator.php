<?php

namespace App\Service;

use Doctrine\Common\Collections\ArrayCollection;

class RecipeGenerator
{
    public function selectedTagsRecipesId(array $allRecipes, ArrayCollection $titles) :array
    {
        $titles = iterator_to_array($titles);
        $neededRecipeId = array();
        foreach ($titles as $title) {
            foreach ($allRecipes as $recipe) {
                foreach ($recipe->getTags() as $tag) {
                    if ($title == $tag) {
                        $neededRecipeId[] = $recipe->getId();
                        break;
                    }
                }
            }
        }
        $neededRecipeId = array_unique($neededRecipeId);
        return $neededRecipeId;
    }

    public function randomizeSelectedTagsRecipesId(array $neededRecipeId) :array
    {
        $randomRecipeId = array();
        //Darom cikla, kuris runnina per 7kart, nes 7 dienos, if neededRecipesId count = 0 break;
        for ($i=0; $i<7; $i++) {
            $randomValue = $neededRecipeId[array_rand($neededRecipeId)];
            $randomRecipeId[] = $randomValue;
            $key = array_search($randomValue, $neededRecipeId);
            unset($neededRecipeId[$key]);
            if(count($neededRecipeId) == 0){
                break;
            }
        }
        return $randomRecipeId;
    }
}