<?php

namespace App\Service;

use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;

class RecipesGenerator
{
    const DAY_COUNT = 7;

    private $recipeRepository;

    public function __construct(RecipeRepository $recipeRepository)
    {
        $this->recipeRepository = $recipeRepository;
    }

    public function getGeneratedRecipesId(ArrayCollection $selectedTags)
    {
        if (count($selectedTags) != 0) {
            $selectedTags = iterator_to_array($selectedTags);

            if (in_array('Meat', $selectedTags) == false && in_array('Vegetarian', $selectedTags)) {
                // Get only vegan recipes
                if (count($selectedTags) == 1) {
                    return $this->recipeRepository->getNeededVeganId();
                } else {
                    $selectedTags =  array_diff($selectedTags, (array)'Vegetarian');
                    $generatedRecipeId = $this->recipeRepository->getNeededId($selectedTags);
                    $remainingRecipeId = $this->recipeRepository->getRemainingVeganRecipeId(
                        $generatedRecipeId,
                        self::DAY_COUNT - count($generatedRecipeId)
                    );
                    $generatedRecipeId = array_merge($generatedRecipeId, $remainingRecipeId);
                    return $generatedRecipeId;
                }
            } else {
                $generatedRecipeId = $this->recipeRepository->getNeededId($selectedTags);

                if (count($generatedRecipeId) < self::DAY_COUNT) {
                    $remainingRecipeId = $this->recipeRepository->getRemainingRecipeId(
                        $generatedRecipeId,
                        self::DAY_COUNT - count($generatedRecipeId)
                    );
                    $generatedRecipeId = array_merge($generatedRecipeId, $remainingRecipeId);
                }
            }
        } else {
            $remainingRecipeId =$this->recipeRepository->getRemainingRecipeId(array(0), self::DAY_COUNT);
            $generatedRecipeId = $remainingRecipeId;
        }

        return $generatedRecipeId;
    }

    public function getGeneratedRecipes(array $generatedRecipeId)
    {
        $selectedTagRecipes =  $this->recipeRepository->findBy([
            'id' => $generatedRecipeId
        ]);

       // shuffle($selectedTagRecipes);

        return $selectedTagRecipes;
    }
}
