<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use mysql_xdevapi\DatabaseObject;

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
            ->from('App\Entity\Recipe', 'recipe')
        ;

        $query = $qb->getQuery();

        return $query->getSingleScalarResult();
    }

    public function getNeededId(ArrayCollection $tags)
    {
        $tags = iterator_to_array($tags);

        $id_params = array();
        foreach ($tags as $tag) {
            // generate a unique name for this parameter
            $name = "'$tag'"; // ":id_0", ":id_1", etc.

            // set the value
            $params[$name] = $tag;

            // and keep track of the name
            $id_params[] = $name;
        }

// next prepare the parameter names for placement in the query string
        $id_params = implode(',', $id_params);


        $conn = $this->getEntityManager()->getConnection();
        $sql = "
        select  DISTINCT recipe_id
from
     (SELECT recipe_id,tag_id
         FROM recipe, tag, recipe_tag
         where tag.id = recipe_tag.tag_id AND tag.title IN ($id_params)
         ORDER BY RAND()) as z
group by z.tag_id
order by rand()
LIMIT 7;
        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

    public function getRemainingRecipeId(array $neededRecipeId, int $count)
    {
     //   var_dump($neededRecipeId);
        foreach ($neededRecipeId as $needed){
            echo $needed . ' ++++ ';
        }

        var_dump($neededRecipeId);
        $id_params = array();
        foreach ($neededRecipeId as $tag) {
            echo  $tag . '+++';

            // generate a unique name for this parameter
            $name = "$tag"; // ":id_0", ":id_1", etc.

            // set the value
            $params[$name] = $tag;

            // and keep track of the name
            $id_params[] = $name;
        }


// next prepare the parameter names for placement in the query string
        $id_params = implode(',', $id_params);
        $conn = $this->getEntityManager()->getConnection();
        $sql = "
select  DISTINCT recipe_id
from recipe, tag, recipe_tag
         where tag.id = recipe_tag.tag_id AND recipe_tag.recipe_id NOT IN ($id_params)
order by rand()
LIMIT $count;

        ";
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // returns an array of arrays (i.e. a raw data set)
        return $stmt->fetchAll(\PDO::FETCH_COLUMN);
    }

}
