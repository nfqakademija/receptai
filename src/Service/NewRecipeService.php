<?php

namespace App\Service;

class NewRecipeService
{
    public function determineIfMeatAndVegetarianTag(array $tagArray) :bool
    {
        if (in_array('Beef', $tagArray) && in_array('Vegetarian', $tagArray)) {
            return true;
        }
        if (in_array('Chicken', $tagArray) && in_array('Vegetarian', $tagArray)) {
            return true;
        }
        if (in_array('Dessert', $tagArray) && in_array('Vegetarian', $tagArray)) {
            return true;
        }
        if (in_array('Fish', $tagArray) && in_array('Vegetarian', $tagArray)) {
            return true;
        }
        if (in_array('Lamb', $tagArray) && in_array('Vegetarian', $tagArray)) {
            return true;
        }
        return false;
    }
}
