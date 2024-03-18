<?php

namespace App\Repository\Module\Making;

use App\Entity\Module\Making\MakingI18n;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * MakingI18nRepository.
 *
 * @extends ServiceEntityRepository<MakingI18n>
 *
 * @method MakingI18n|null find($id, $lockMode = null, $lockVersion = null)
 * @method MakingI18n|null findOneBy(array $criteria, array $orderBy = null)
 * @method MakingI18n[]    findAll()
 * @method MakingI18n[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class MakingI18nRepository extends ServiceEntityRepository
{
    /**
     * MakingI18nRepository constructor.
     */
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($this->registry, MakingI18n::class);
    }
}
