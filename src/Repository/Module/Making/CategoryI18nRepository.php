<?php

namespace App\Repository\Module\Making;

use App\Entity\Module\Making\CategoryI18n;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * CategoryI18nRepository.
 *
 * @extends ServiceEntityRepository<CategoryI18n>
 *
 * @method CategoryI18n|null find($id, $lockMode = null, $lockVersion = null)
 * @method CategoryI18n|null findOneBy(array $criteria, array $orderBy = null)
 * @method CategoryI18n[]    findAll()
 * @method CategoryI18n[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class CategoryI18nRepository extends ServiceEntityRepository
{
    /**
     * CategoryMediaRelation constructor.
     */
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($this->registry, CategoryI18n::class);
    }
}
