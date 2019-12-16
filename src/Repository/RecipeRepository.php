<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Recipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Recipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Recipe[]    findAll()
 * @method Recipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findRecipeCount()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb
            ->select('count(recipe.id)')
            ->from(Recipe::class, 'recipe');

        $query = $qb->getQuery();

        return $query->getSingleScalarResult();
    }

    public function getNeededId(array $tags)
    {
        $tag_params = array();
        foreach ($tags as $tag) {
            $name = "'$tag'"; // "'Breakfast'", "'Beef", etc.

            $params[$name] = $tag;

            $tag_params[] = $name;
        }

        $tag_params = implode(',', $tag_params);

        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        select  DISTINCT recipe_id
        from
         (SELECT recipe_id,tag_id
         FROM recipe, tag, recipe_tag
         where tag.id = recipe_tag.tag_id AND tag.title IN ($tag_params)
         ORDER BY RAND()) as z
        group by z.tag_id
        order by rand()
        LIMIT 7;
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getNeededVeganId()
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        select  DISTINCT recipe_id
        from recipe, tag, recipe_tag
        where tag.id = recipe_tag.tag_id AND tag.title = 'Vegetarian'
        order by rand()
        LIMIT 7;
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getRemainingVeganRecipeId(array $neededRecipeId, int $count)
    {
        $recipeId_params = array();
        foreach ($neededRecipeId as $recipeId) {
            $id = "$recipeId"; // "8", "4", etc.

            $params[$id] = $recipeId;

            $recipeId_params[] = $id;
        }

        $recipeId_params = implode(',', $recipeId_params);

        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        select  DISTINCT recipe_id
        from recipe, tag, recipe_tag
        where tag.id = recipe_tag.tag_id AND recipe_tag.recipe_id NOT IN ($recipeId_params) AND tag.title = 'Vegetarian'
        order by rand()
        LIMIT $count;
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
    public function getRemainingRecipeId(array $neededRecipeId, int $count)
    {
        $recipeId_params = array();
        foreach ($neededRecipeId as $recipeId) {
            $id = "$recipeId"; // "8", "4", etc.

            $params[$id] = $recipeId;

            $recipeId_params[] = $id;
        }

        $recipeId_params = implode(',', $recipeId_params);

        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        select  DISTINCT recipe_id
        from recipe, tag, recipe_tag
        where tag.id = recipe_tag.tag_id AND recipe_tag.recipe_id NOT IN ($recipeId_params)
        order by rand()
        LIMIT $count;
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getRerolledAJaxId(object $tags, array $neededRecipeId)
    {
        $recipeId_params = array();
        foreach ($neededRecipeId as $recipeId) {
            $id = "$recipeId"; // "8", "4", etc.

            $params[$id] = $recipeId;

            $recipeId_params[] = $id;
        }
        $tag_params = array();
        foreach ($tags as $tag) {
            $name = "'$tag'"; // "'Breakfast'", "'Beef", etc.

            $params[$name] = $tag;

            $tag_params[] = $name;
        }

        $tag_params = implode(',', $tag_params);

        $recipeId_params = implode(',', $recipeId_params);

        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        select  DISTINCT recipe_id
        from recipe, tag, recipe_tag
        where tag.id = recipe_tag.tag_id
        AND recipe_tag.recipe_id NOT IN ($recipeId_params) AND tag.title IN ($tag_params)
        order by rand()
        LIMIT 1;
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getRerolledAJaxVeganId(array $neededRecipeId)
    {
        $recipeId_params = array();
        foreach ($neededRecipeId as $recipeId) {
            $id = "$recipeId"; // "8", "4", etc.

            $params[$id] = $recipeId;

            $recipeId_params[] = $id;
        }

        $recipeId_params = implode(',', $recipeId_params);

        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        select  DISTINCT recipe_id
        from recipe, tag, recipe_tag
        where tag.id = recipe_tag.tag_id
        AND recipe_tag.recipe_id NOT IN ($recipeId_params) AND tag.title = 'Vegetarian'
        order by rand()
        LIMIT 1;
        ";

        $stmt = $conn->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }
}
