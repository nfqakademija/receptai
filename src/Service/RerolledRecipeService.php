<?php

namespace App\Service;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;

class RerolledRecipeService
{
    private $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    public function getNeededRerollId(ArrayCollection $selectedTags, array $generatedRecipeIds, object $tags)
    {
        $selectedTags = iterator_to_array($selectedTags);
        if (in_array('Vegetarian', $selectedTags) && count($selectedTags) == 1) {
            $rerolledRecipeId = $this->recipeRepository->getRerolledAJaxVeganId($generatedRecipeIds);
        } else {
            $rerolledRecipeId = $this->recipeRepository->getRerolledAJaxId($tags, $generatedRecipeIds);
        }
        return array_shift($rerolledRecipeId);
    }
}