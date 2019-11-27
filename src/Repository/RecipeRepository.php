<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ManagerRegistry;
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

    public function findRecipesWithTags(array $selectedTagTitles)
    {

        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();
        $qb->select('i')
            ->from('App\Entity\Recipe', 'i')
            ->where("i.tags IN (:listCat)")
            ->setParameter('listCat', $selectedTagTitles);

        return $qb->getQuery()->getResult();


    }
}
