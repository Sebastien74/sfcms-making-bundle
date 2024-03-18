<?php

namespace App\Controller\Admin\Module\Making;

use App\Controller\Admin\AdminController;
use App\Entity\Module\Making\Listing;
use App\Form\Type\Module\Making\ListingType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * ListingController.
 *
 * Making Category Action management
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[IsGranted('ROLE_MAKING')]
#[Route('/admin-%security_token%/{website}/makings/listings', schemes: '%protocol%')]
class ListingController extends AdminController
{
    protected ?string $class = Listing::class;
    protected ?string $formType = ListingType::class;

    /**
     * Index Listing.
     *
     * {@inheritdoc}
     */
    #[Route('/index', name: 'admin_makinglisting_index', methods: 'GET|POST')]
    public function index(Request $request, PaginatorInterface $paginator)
    {
        return parent::index($request, $paginator);
    }

    /**
     * New Listing.
     *
     * {@inheritdoc}
     */
    #[Route('/new', name: 'admin_makinglisting_new', methods: 'GET|POST')]
    public function new(Request $request)
    {
        return parent::new($request);
    }

    /**
     * Edit Listing.
     *
     * {@inheritdoc}
     */
    #[Route('/edit/{makinglisting}', name: 'admin_makinglisting_edit', methods: 'GET|POST')]
    public function edit(Request $request)
    {
        return parent::edit($request);
    }

    /**
     * Show Listing.
     *
     * {@inheritdoc}
     */
    #[Route('/show/{makinglisting}', name: 'admin_makinglisting_show', methods: 'GET')]
    public function show(Request $request)
    {
        return parent::show($request);
    }

    /**
     * Position Listing.
     *
     * {@inheritdoc}
     */
    #[Route('/position/{makinglisting}', name: 'admin_makinglisting_position', methods: 'GET|POST')]
    public function position(Request $request)
    {
        return parent::position($request);
    }

    /**
     * Delete Listing.
     *
     * {@inheritdoc}
     */
    #[Route('/delete/{makinglisting}', name: 'admin_makinglisting_delete', methods: 'DELETE')]
    public function delete(Request $request)
    {
        return parent::delete($request);
    }
}
