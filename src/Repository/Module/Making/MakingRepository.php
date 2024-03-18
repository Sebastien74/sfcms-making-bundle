<?php

namespace App\Repository\Module\Making;

use App\Entity\Core\Website;
use App\Entity\Module\Making\Listing;
use App\Entity\Module\Making\Making;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * MakingRepository.
 *
 * @extends ServiceEntityRepository<Making>
 *
 * @method Making|null find($id, $lockMode = null, $lockVersion = null)
 * @method Making|null findOneBy(array $criteria, array $orderBy = null)
 * @method Making[]    findAll()
 * @method Making[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class MakingRepository extends ServiceEntityRepository
{
    /**
     * MakingRepository constructor.
     */
    public function __construct(private readonly ManagerRegistry $registry)
    {
        parent::__construct($this->registry, Making::class);
    }

    /**
     * Find one by filter.
     *
     * @throws NonUniqueResultException
     */
    public function findOneByFilter(Website $website, string $locale, mixed $filter): ?Making
    {
        $statement = $this->createQueryBuilder('m')
            ->leftJoin('m.website', 'w')
            ->andWhere('m.website = :website')
            ->setParameter('website', $website)
            ->addSelect('w');

        if (is_numeric($filter)) {
            $statement->andWhere('m.id = :id')
                ->setParameter('id', $filter);
        } else {
            $statement->andWhere('m.slug = :slug')
                ->setParameter('slug', $filter);
        }

        return $statement->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find Newscast by url & locale.
     *
     * @throws NonUniqueResultException
     */
    public function findByUrlAndLocale(string $url, Website $website, string $locale, bool $preview = false): ?Making
    {
        return $this->optimizedQueryBuilder($locale, $website, null, null, $preview)
            ->andWhere('u.code = :code')
            ->setParameter('code', $url)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find all published by Category order newest.
     *
     * @return Making[]
     */
    public function findByListing(string $locale, Website $website, Listing $listing, Making $excludeMaking = null): array
    {
        $orderBy = explode('-', $listing->getOrderBy());

        $qb = $this->optimizedQueryBuilder($locale, $website, $orderBy[0], strtoupper($orderBy[1]));

        $categoryIds = [];
        foreach ($listing->getCategories() as $category) {
            $categoryIds[] = $category->getId();
        }
        if ($categoryIds) {
            $qb->leftJoin('m.category', 'category')
                ->andWhere('category.id IN (:categoryIds)')
                ->setParameter('categoryIds', $categoryIds);
        }

        if ($excludeMaking) {
            $qb->andWhere('m.id != :excludeId')
                ->setParameter('excludeId', $excludeMaking->getId());
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * Find max result published order newest by Category.
     *
     * @return Making|Making[]|null
     *
     * @throws NonUniqueResultException
     */
    public function findMaxResultPublishedListingOrderByNewest(string $locale, Website $website, Listing $listing, int $limit = 5): Making|array|null
    {
        if ($listing->getCategories()->isEmpty()) {
            return null;
        }

        $orderBy = explode('-', $listing->getOrderBy());

        $qb = $this->optimizedQueryBuilder($locale, $website, $orderBy[0], strtoupper($orderBy[1]))
            ->setMaxResults($limit);

        foreach ($listing->getCategories() as $key => $category) {
            $qb->andWhere('c.id = :categoryId'.$key)
                ->setParameter('categoryId'.$key, $category->getId());
        }

        if (1 === $limit) {
            return $qb->getQuery()
                ->getOneOrNullResult();
        }

        return $qb->getQuery()
            ->getResult();
    }

    /**
     * PublishedQueryBuilder.
     */
    public function optimizedQueryBuilder(
        string $locale,
        Website $website,
        string $sort = null,
        string $order = null,
        bool $preview = false,
        QueryBuilder $qb = null): QueryBuilder
    {
        $sort = $sort ? $sort : 'publicationStart';
        $order = $order ? $order : 'DESC';

        $statement = $this->getOrCreateQueryBuilder($qb)
            ->leftJoin('m.website', 'w')
            ->leftJoin('m.urls', 'u')
            ->leftJoin('u.seo', 's')
            ->leftJoin('m.category', 'c')
            ->andWhere('m.website = :website')
            ->andWhere('u.locale = :locale')
            ->setParameter('website', $website)
            ->setParameter('locale', $locale)
            ->addSelect('w')
            ->addSelect('u')
            ->addSelect('s')
            ->addSelect('c')
            ->orderBy('m.'.$sort, $order);

        if (!$preview) {
            $statement->andWhere('m.publicationStart IS NULL OR m.publicationStart < CURRENT_TIMESTAMP()')
                ->andWhere('m.publicationEnd IS NULL OR m.publicationEnd > CURRENT_TIMESTAMP()')
                ->andWhere('m.publicationStart IS NOT NULL')
                ->andWhere('u.online = :online')
                ->setParameter('online', true);
        }

        return $statement;
    }

    /**
     * Main QueryBuilder.
     */
    private function getOrCreateQueryBuilder(QueryBuilder $qb = null): QueryBuilder
    {
        return $qb ?: $this->createQueryBuilder('m');
    }
}
