<?php

namespace App\Repository;

use App\Entity\Recipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Recipe>
 */
class RecipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Recipe::class);
    }
   
    /**
     * This function is used to find public recipes based on the number of recipes.
     * @param int $nbRecipes
     * @return array
     */
    public function findPublicRecipe(?int $nbRecipes): array
    {
        $queryBuilder =  $this->createQueryBuilder('r')
            ->andWhere('r.isPublic = 1')
            ->orderBy('r.createdAt', 'DESC');
            if ($nbRecipes > 0 && $nbRecipes !== null) {
                # code...
                $queryBuilder->setMaxResults($nbRecipes);
            }

        return $queryBuilder->getQuery()
            ->getResult();
    }

}
