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
     */
    public function allRecipesCount(): int
    {
        $entityManager = $this->getEntityManager();

        $query =  $entityManager->createQueryBuilder('a')
            ->from('App\Entity\Recipe a')
            ->select('count(a.id)')
            ->getQuery()
            ->getSingleScalarResult();
        // returns an array of Product objects
        return $query->getResult();
    }
}
