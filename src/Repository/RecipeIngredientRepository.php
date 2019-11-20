<?php

namespace App\Repository;

use App\Entity\RecipeIngredient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method RecipeIngredient|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecipeIngredient|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecipeIngredient[]    findAll()
 * @method RecipeIngredient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeIngredientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecipeIngredient::class);
    }

    public function findSum(array $neededRecipesId)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('sum(recipe.amount) as total')
            ->from('App\Entity\RecipeIngredient', 'recipe')
            ->where('recipe.recipe IN (:neededRecipesId)')
            ->setParameter('neededRecipesId', $neededRecipesId)
            ->addSelect('recipe')
            ->groupBy('recipe.ingredient')
        ;

        $query = $qb->getQuery();

        return $query->getResult();

    }
}
