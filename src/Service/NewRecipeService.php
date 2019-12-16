<?php

namespace App\Service;

class NewRecipeService
{
    public function determineIfMeatAndVegetarianTag(array $tagArray) :bool
    {
        if (in_array('Meat', $tagArray) && in_array('Vegetarian', $tagArray)) {
            return true;
        }
        return false;
    }
}
