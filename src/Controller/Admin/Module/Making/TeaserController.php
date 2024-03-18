<?php

namespace App\Controller\Admin\Module\Making;

use App\Controller\Admin\AdminController;
use App\Entity\Module\Making\Teaser;
use App\Form\Type\Module\Making\TeaserType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * TeaserController.
 *
 * Teaser Action management
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[IsGranted('ROLE_MAKING')]
#[Route('/admin-%security_token%/{website}/makings/teasers', schemes: '%protocol%')]
class TeaserController extends AdminController
{
    protected ?string $class = Teaser::class;
    protected ?string $formType = TeaserType::class;

    /**
     * Index Teaser.
     *
     * {@inheritdoc}
     */
    #[Route('/index', name: 'admin_makingteaser_index', methods: 'GET|POST')]
    public function index(Request $request, PaginatorInterface $paginator)
    {
        return parent::index($request, $paginator);
    }

    /**
     * New Teaser.
     *
     * {@inheritdoc}
     */
    #[Route('/new', name: 'admin_makingteaser_new', methods: 'GET|POST')]
    public function new(Request $request)
    {
        return parent::new($request);
    }

    /**
     * Edit Teaser.
     *
     * {@inheritdoc}
     */
    #[Route('/edit/{makingteaser}', name: 'admin_makingteaser_edit', methods: 'GET|POST')]
    public function edit(Request $request)
    {
        return parent::edit($request);
    }

    /**
     * Show Teaser.
     *
     * {@inheritdoc}
     */
    #[Route('/show/{makingteaser}', name: 'admin_makingteaser_show', methods: 'GET')]
    public function show(Request $request)
    {
        return parent::show($request);
    }

    /**
     * Position Teaser.
     *
     * {@inheritdoc}
     */
    #[Route('/position/{makingteaser}', name: 'admin_makingteaser_position', methods: 'GET|POST')]
    public function position(Request $request)
    {
        return parent::position($request);
    }

    /**
     * Delete Teaser.
     *
     * {@inheritdoc}
     */
    #[Route('/delete/{makingteaser}', name: 'admin_makingteaser_delete', methods: 'DELETE')]
    public function delete(Request $request)
    {
        return parent::delete($request);
    }
}
