<?php

namespace App\Repository\Module\Making;

use App\Entity\Module\Making\MakingMediaRelation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * MakingMediaRelationRepository.
 *
 * @extends ServiceEntityRepository<MakingMediaRelation>
 *
 * @method MakingMediaRelation|null find($id, $lockMode = null, $lockVersion = null)
 * @method MakingMediaRelation|null findOneBy(array $criteria, array $orderBy = null)
 * @method MakingMediaRelation[]    findAll()
 * @method MakingMediaRelation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class MakingMediaRelationRepository extends ServiceEntityRepository
{
    /**
     * MakingMediaRelationRepository constructor.
     */
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($this->registry, MakingMediaRelation::class);
    }
}
