<?php

namespace App\Repository\Module\Making;

use App\Entity\Core\Website;
use App\Entity\Module\Making\Teaser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * TeaserRepository.
 *
 * @extends ServiceEntityRepository<Teaser>
 *
 * @method Teaser|null find($id, $lockMode = null, $lockVersion = null)
 * @method Teaser|null findOneBy(array $criteria, array $orderBy = null)
 * @method Teaser[]    findAll()
 * @method Teaser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class TeaserRepository extends ServiceEntityRepository
{
    /**
     * TeaserRepository constructor.
     */
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($this->registry, Teaser::class);
    }

    /**
     * Find one by filter.
     *
     * @throws NonUniqueResultException
     */
    public function findOneByFilter(Website $website, string $locale, mixed $filter): ?Teaser
    {
        $statement = $this->createQueryBuilder('t')
            ->leftJoin('t.categories', 'c')
            ->leftJoin('t.website', 'w')
            ->addSelect('w')
            ->addSelect('c');

        if (is_numeric($filter)) {
            $statement->andWhere('t.id = :id')
                ->setParameter('id', $filter);
        } else {
            $statement->andWhere('t.slug = :slug')
                ->setParameter('slug', $filter);
        }

        return $statement->getQuery()
            ->getOneOrNullResult();
    }
}
