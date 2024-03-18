<?php

namespace App\Controller\Front\Action;

use App\Controller\Front\FrontController;
use App\Entity\Core\Website;
use App\Entity\Layout\Block;
use App\Entity\Layout\Page;
use App\Entity\Module\Making\Listing;
use App\Entity\Module\Making\Making;
use App\Entity\Module\Making\Teaser;
use App\Entity\Seo\Url;
use App\Repository\Layout\PageRepository;
use App\Repository\Module\Making\ListingRepository;
use App\Repository\Module\Making\MakingRepository;
use App\Repository\Module\Making\TeaserRepository;
use App\Service\Content\ListingService;
use App\Service\Content\SeoService;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\Cache;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * MakingController.
 *
 * Front Makings renders
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class MakingController extends FrontController
{
    /**
     * Index.
     *
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    #[Route('/action/making/index/{website}/{url}/{filter}', name: 'front_making_index', options: ['isMainRequest' => false], methods: 'GET', schemes: '%protocol%')]
    public function index(
        Request $request,
        PaginatorInterface $paginator,
        MakingRepository $makingRepository,
        ListingRepository $listingRepository,
        Website $website,
        Url $url,
        ?Block $block = null,
        mixed $filter = null): JsonResponse|Response
    {
        if (!$filter) {
            return new Response();
        }

        /** @var Listing $listing */
        $listing = $listingRepository->find($filter);

        if (!$listing) {
            return new Response();
        }

        $lastNews = $listing->isLargeFirst()
            ? $makingRepository->findMaxResultPublishedListingOrderByNewest($request->getLocale(), $website, $listing, 1) : null;
        $entities = $makingRepository->findByListing($request->getLocale(), $website, $listing, $lastNews);

        $count = count($entities);
        $limit = $listing->getItemsPerPage() ?: 9;
        $pagination = $this->getPagination($request, $paginator, $entities, $limit);
        $configuration = $website->getConfiguration();
        $template = $configuration->getTemplate();

        $entity = $block instanceof Block ? $block : $listing;
        $entity->setUpdatedAt($listing->getUpdatedAt());

        return $this->render('front/'.$template.'/actions/making/listing.html.twig', [
            'website' => $website,
            'url' => $url,
            'filter' => $filter,
            'listing' => $listing,
            'scrollInfinite' => $listing->isScrollInfinite(),
            'maxPage' => $count > 0 ? intval(ceil($count / $limit)) : $count,
            'lastNews' => $lastNews,
            'websiteTemplate' => $template,
            'thumbConfiguration' => $this->thumbConfiguration($website, Making::class, 'index'),
            'entities' => $pagination,
            'allEntities' => $lastNews ? array_merge([$lastNews], $entities) : $entities,
        ]);
    }

    /**
     * Teaser.
     *
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function teaser(
        Request $request,
        TeaserRepository $teaserRepository,
        ListingService $listingService,
        Website $website,
        Block $block,
        Url $url,
        mixed $filter = null): Response
    {
        if (!$filter) {
            return new Response();
        }

        /** @var Teaser $teaser */
        $teaser = $teaserRepository->findOneByFilter($website, $request->getLocale(), $filter);

        if (!$teaser) {
            return new Response();
        }

        $configuration = $website->getConfiguration();
        $template = $configuration->getTemplate();
        $locale = $request->getLocale();
        $entities = $listingService->findTeaserEntities($teaser, $locale, Making::class, $website);

        $entity = $block instanceof Block ? $block : $teaser;
        $entity->setUpdatedAt($teaser->getUpdatedAt());

        return $this->render('front/'.$template.'/actions/making/teaser.html.twig', [
            'websiteTemplate' => $template,
            'block' => $block,
            'url' => $url,
            'website' => $website,
            'urlsIndex' => $listingService->indexesPages($teaser, $locale, Listing::class, Making::class, $entities, []),
            'teaser' => $teaser,
            'entities' => $entities,
            'thumbConfiguration' => $this->thumbConfiguration($website, Making::class, 'teaser'),
        ]);
    }

    /**
     * Comparator.
     *
     * @throws NonUniqueResultException
     * @throws \Exception
     */
    public function comparator(
        Request $request,
        MakingRepository $makingRepository,
        Website $website,
        Block $block,
        Url $url,
        mixed $filter = null): Response
    {
        if (!$filter) {
            return new Response();
        }

        /** @var Making $making */
        $making = $makingRepository->findOneByFilter($website, $request->getLocale(), $filter);

        if (!$making instanceof Making) {
            return new Response();
        }

        $configuration = $website->getConfiguration();
        $template = $website->getConfiguration()->getTemplate();

        return $this->render('front/'.$template.'/actions/making/comparator.html.twig', [
            'websiteTemplate' => $template,
            'block' => $block,
            'url' => $url,
            'website' => $website,
            'making' => $making,
            'thumbConfiguration' => $this->thumbConfiguration($website, Making::class, 'comparator'),
        ]);
    }

    /**
     * View.
     *
     * @return Response
     *
     * @throws InvalidArgumentException
     * @throws NonUniqueResultException
     */
    #[Route([
        'fr' => '/{pageUrl}/fiche-realisation/{url}',
        'en' => '/{pageUrl}/making-card/{url}',
    ], name: 'front_making_view', methods: 'GET', schemes: '%protocol%', priority: 300)]
    #[Route([
        'fr' => '/fiche-realisation/{url}',
        'en' => '/making-card/{url}',
    ], name: 'front_making_view_only', methods: 'GET', schemes: '%protocol%', priority: 300)]
    #[Cache(expires: 'tomorrow', public: true)]
    public function view(
        Request $request,
        MakingRepository $makingRepository,
        PageRepository $pageRepository,
        ListingService $listingService,
        SeoService $seoService,
        string $url,
        ?string $pageUrl = null,
        ?Website $website = null,
        bool $preview = false)
    {
        $website = $website ?: $this->getWebsite($request, true);
        $making = $makingRepository->findByUrlAndLocale($url, $website, $request->getLocale(), $preview);

        if (!$making instanceof Making) {
            throw $this->createNotFoundException();
        }

        $url = $making->getUrls()->first();
        $request->setLocale($url->getLocale());

        /* To redirect if pageUrl is empty */
        if (!$pageUrl) {
            $indexUrls = $listingService->indexesPages($making, $request->getLocale(), Listing::class, Making::class, [$making]);
            $pageUrl = $indexUrls[$making->getId()] ?? null;
            if ($pageUrl) {
                return $this->redirectToRoute('front_making_view', ['url' => $url->getCode(), 'pageUrl' => $pageUrl]);
            }
        }

        /** @var Page $page */
        $page = $pageUrl ? $pageRepository->findByUrlCodeAndLocale($website, $pageUrl, $request->getLocale(), $preview) : $pageRepository->findIndex($website, $request->getLocale());
        $websiteTemplate = $website->getConfiguration()->getTemplate();
        $thumbConfigurationHeader = $this->thumbConfiguration($website, Making::class, 'view', null, 'title-header');
        $thumbConfigurationHeader = $thumbConfigurationHeader ?: $this->thumbConfiguration($website, Block::class, null, 'title-header');

        return $this->render('front/'.$websiteTemplate.'/actions/making/view.html.twig', [
            'website' => $website,
            'interface' => $this->getInterface(Making::class),
            'websiteTemplate' => $websiteTemplate,
            'seo' => $seoService->execute($url, $making),
            'thumbConfiguration' => $this->thumbConfiguration($website, Making::class, 'view'),
            'thumbConfigurationHeader' => $thumbConfigurationHeader,
            'page' => $page,
            'url' => $url,
            'making' => $making,
        ]);
    }

    /**
     * Preview.
     *
     * @throws NonUniqueResultException
     */
    #[IsGranted('ROLE_ADMIN')]
    #[Route('/admin-%security_token%/front/making/preview/{website}/{url}', name: 'front_making_preview', methods: 'GET|POST', schemes: '%protocol%')]
    public function preview(Request $request, ListingService $listingService, MakingRepository $makingRepository, Website $website, Url $url): Response
    {
        if (!$url->getCode()) {
            throw $this->createNotFoundException($this->coreLocator->translator()->trans('Vous devez renseigner un code URL.', [], 'admin'));
        }
        $making = $makingRepository->findByUrlAndLocale($url->getCode(), $website, $url->getLocale(), true);
        if (!$making) {
            throw $this->createNotFoundException();
        }
        $indexUrls = $listingService->indexesPages($making, $url->getLocale(), Listing::class, Making::class, [$making]);
        $request->setLocale($url->getLocale());

        return $this->forward(MakingController::class.'::view', [
            'pageUrl' => !empty($indexUrls[$making->getId()]) ? $indexUrls[$making->getId()] : null,
            'url' => $url->getCode(),
            'website' => $website,
            'preview' => true,
        ]);
    }
}
