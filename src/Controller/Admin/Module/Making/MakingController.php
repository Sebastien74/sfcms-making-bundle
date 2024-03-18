<?php

namespace App\Controller\Admin\Module\Making;

use App\Controller\Admin\AdminController;
use App\Entity\Layout\BlockType;
use App\Entity\Module\Making\Making;
use App\Form\Interface\ModuleFormManagerInterface;
use App\Form\Type\Module\Making\MakingType;
use App\Service\Interface\AdminLocatorInterface;
use App\Service\Interface\CoreLocatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * MakingController.
 *
 * Making Action management
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[IsGranted('ROLE_MAKING')]
#[Route('/admin-%security_token%/{website}/makings', schemes: '%protocol%')]
class MakingController extends AdminController
{
    protected ?string $class = Making::class;
    protected ?string $formType = MakingType::class;

    /**
     * MakingController constructor.
     */
    public function __construct(
        protected ModuleFormManagerInterface $moduleFormInterface,
        protected CoreLocatorInterface $baseLocator,
        protected AdminLocatorInterface $adminLocator
    ) {
        $this->formManager = $moduleFormInterface->making();
        parent::__construct($baseLocator, $adminLocator);
    }

    /**
     * Index Making.
     *
     * {@inheritdoc}
     */
    #[Route('/index', name: 'admin_making_index', methods: 'GET|POST')]
    public function index(Request $request, PaginatorInterface $paginator)
    {
        return parent::index($request, $paginator);
    }

    /**
     * New Making.
     *
     * {@inheritdoc}
     */
    #[Route('/new', name: 'admin_making_new', methods: 'GET|POST')]
    public function new(Request $request)
    {
        return parent::new($request);
    }

    /**
     * Edit Making.
     *
     * {@inheritdoc}
     */
    #[Route('/edit/{making}', name: 'admin_making_edit', methods: 'GET|POST')]
    #[Route('/layout/{making}', name: 'admin_making_layout', methods: 'GET|POST')]
    public function edit(Request $request)
    {
        $mediasCategoriesActive = $this->getWebsite($request)->getConfiguration()->isMediasCategoriesStatus();
        if (!$mediasCategoriesActive) {
            $session = new Session();
            $session->getFlashBag()->add('warning', $this->coreLocator->translator()->trans('Vous devez activer les catégories de médias dans la configration du site.', [], 'admin'));
        }
        $this->arguments['blockTypesDisabled'] = ['layout' => ['']];
        $this->arguments['blockTypesCategories'] = ['layout', 'content', 'global', 'action', 'modules'];
        $this->arguments['blockTypeAction'] = $this->coreLocator->em()->getRepository(BlockType::class)->findOneBy(['slug' => 'core-action']);
        $this->template = 'admin/page/making/making-edit.html.twig';
        return parent::edit($request);
    }

    /**
     * Medias Making.
     */
    #[Route('/medias/{making}', name: 'admin_making_medias', methods: 'GET|POST')]
    public function medias(Request $request): Response
    {
        return $this->render('admin/page/making/making-medias.html.twig', [
            'entity' => $this->coreLocator->em()->getRepository(Making::class)->find($request->get('making')),
            'website' => $this->getWebsite($request),
            'interface' => $this->getInterface($this->class),
        ]);
    }

    /**
     * Position Making.
     *
     * {@inheritdoc}
     */
    #[Route('/position/{making}', name: 'admin_making_position', methods: 'GET|POST')]
    public function position(Request $request)
    {
        return parent::position($request);
    }

    /**
     * Delete Making.
     *
     * {@inheritdoc}
     */
    #[Route('/delete/{making}', name: 'admin_making_delete', methods: 'DELETE')]
    public function delete(Request $request)
    {
        return parent::delete($request);
    }
}