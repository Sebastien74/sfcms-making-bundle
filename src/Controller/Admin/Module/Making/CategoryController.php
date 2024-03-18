<?php

namespace App\Controller\Admin\Module\Making;

use App\Controller\Admin\AdminController;
use App\Entity\Module\Making\Category;
use App\Form\Type\Module\Making\CategoryType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * CategoryController.
 *
 * Making Category Action management
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[IsGranted('ROLE_MAKING')]
#[Route('/admin-%security_token%/{website}/makings/categories', schemes: '%protocol%')]
class CategoryController extends AdminController
{
    protected ?string $class = Category::class;
    protected ?string $formType = CategoryType::class;

    /**
     * Index Category.
     *
     * {@inheritdoc}
     */
    #[Route('/index', name: 'admin_makingcategory_index', methods: 'GET|POST')]
    public function index(Request $request, PaginatorInterface $paginator)
    {
        return parent::index($request, $paginator);
    }

    /**
     * New Category.
     *
     * {@inheritdoc}
     */
    #[Route('/new', name: 'admin_makingcategory_new', methods: 'GET|POST')]
    public function new(Request $request)
    {
        return parent::new($request);
    }

    /**
     * Layout Category.
     *
     * {@inheritdoc}
     */
    #[Route('/layout/{makingcategory}', name: 'admin_makingcategory_layout', methods: 'GET|POST')]
    public function layout(Request $request)
    {
        return parent::layout($request);
    }

    /**
     * Show Category.
     *
     * {@inheritdoc}
     */
    #[Route('/show/{makingcategory}', name: 'admin_makingcategory_show', methods: 'GET')]
    public function show(Request $request)
    {
        return parent::show($request);
    }

    /**
     * Position Category.
     *
     * {@inheritdoc}
     */
    #[Route('/position/{makingcategory}', name: 'admin_makingcategory_position', methods: 'GET|POST')]
    public function position(Request $request)
    {
        return parent::position($request);
    }

    /**
     * Delete Category.
     *
     * {@inheritdoc}
     */
    #[Route('/delete/{makingcategory}', name: 'admin_makingcategory_delete', methods: 'DELETE')]
    public function delete(Request $request)
    {
        return parent::delete($request);
    }
}
