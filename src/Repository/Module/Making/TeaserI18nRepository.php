<?php

namespace App\Repository\Module\Making;

use App\Entity\Module\Making\TeaserI18n;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * TeaserI18nRepository.
 *
 * @extends ServiceEntityRepository<TeaserI18n>
 *
 * @method TeaserI18n|null find($id, $lockMode = null, $lockVersion = null)
 * @method TeaserI18n|null findOneBy(array $criteria, array $orderBy = null)
 * @method TeaserI18n[]    findAll()
 * @method TeaserI18n[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class TeaserI18nRepository extends ServiceEntityRepository
{
    /**
     * TeaserI18nRepository constructor.
     */
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($this->registry, TeaserI18n::class);
    }
}
