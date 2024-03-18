<?php

namespace App\Repository\Module\Making;

use App\Entity\Module\Making\CategoryMediaRelation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * CategoryMediaRelationRepository.
 *
 * @extends ServiceEntityRepository<CategoryMediaRelation>
 *
 * @method CategoryMediaRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryMediaRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryMediaRelation[]    findAll()
 * @method CategoryMediaRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class CategoryMediaRelationRepository extends ServiceEntityRepository
{
    /**
     * CategoryMediaRelation constructor.
     */
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($this->registry, CategoryMediaRelation::class);
    }
}
