<?php

namespace App\Form\Type\Module\Making;

use App\Entity\Module\Making\Category;
use App\Entity\Module\Making\Making;
use App\Form\Widget as WidgetType;
use App\Service\Interface\CoreLocatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * MakingType.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class MakingType extends AbstractType
{
    private TranslatorInterface $translator;
    private EntityManagerInterface $entityManager;
    private bool $isLayoutUser;

    /**
     * MakingType constructor.
     */
    public function __construct(
        private readonly CoreLocatorInterface $coreLocator,
        private readonly TokenStorageInterface $tokenStorage
    ) {
        $this->translator = $this->coreLocator->translator();
        $this->entityManager = $this->coreLocator->em();
        $user = !empty($this->tokenStorage->getToken()) ? $this->tokenStorage->getToken()->getUser() : null;
        $this->isLayoutUser = $user && in_array('ROLE_LAYOUT_MAKING', $user->getRoles());
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Making $data */
        $data = $builder->getData();
        $isNew = !$data->getId();
        $website = $options['website'];
        $displayCategory = count($this->entityManager->getRepository(Category::class)->findBy(['website' => $website])) > 1;

        $adminNameClass = $isNew ? 'col-md-9' : 'col-12';
        if (!$displayCategory) {
            $adminNameClass = $isNew ? 'col-12' : 'col-md-10';
        }

        $adminName = new WidgetType\AdminNameType($this->coreLocator);
        $adminName->add($builder, [
            'adminNameGroup' => $adminNameClass,
            'class' => 'refer-code',
        ]);

        $builder->add('category', EntityType::class, [
            'required' => $displayCategory,
            'label' => $this->translator->trans('Catégorie', [], 'admin'),
            'display' => 'search',
            'placeholder' => $this->translator->trans('Sélectionnez', [], 'admin'),
            'attr' => [
                'data-placeholder' => $this->translator->trans('Sélectionnez', [], 'admin'),
                'group' => $displayCategory ? 'col-md-3' : 'd-none',
            ],
            'class' => Category::class,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->orderBy('c.adminName', 'ASC');
            },
            'choice_label' => function ($entity) {
                return strip_tags($entity->getAdminName());
            },
            'constraints' => $displayCategory ? [new Assert\NotBlank()] : [],
        ]);

        if ($isNew && $this->isLayoutUser) {
            $builder->add('customLayout', Type\CheckboxType::class, [
                'required' => false,
                'display' => 'button',
                'color' => 'outline-info-darken-dark',
                'label' => $this->translator->trans('Template personnalisé', [], 'admin'),
                'attr' => ['class' => 'w-100', 'group' => 'col-md-3 d-flex mx-auto justify-content-center'],
            ]);
        }

        if (!$isNew) {

            $builder->add('promote', Type\CheckboxType::class, [
                'required' => false,
                'display' => 'button',
                'color' => 'outline-info-darken-dark',
                'label' => $this->translator->trans('Mettre en avant', [], 'admin'),
                'attr' => ['group' => 'col-md-2 d-flex align-items-end', 'class' => 'w-100'],
            ]);

            if (!$data->isCustomLayout()) {
                $i18ns = new WidgetType\i18nsCollectionType($this->coreLocator);
                $i18ns->add($builder, [
                    'website' => $options['website'],
                    'fields' => ['title' => 'col-md-4', 'subTitle' => 'col-md-4', 'help' => 'col-md-4', 'introduction', 'body', 'video', 'targetLink' => 'col-md-12 add-title', 'targetPage' => 'col-md-4', 'targetLabel' => 'col-md-4', 'targetStyle' => 'col-md-4', 'newTab' => 'col-md-4'],
                    'label_fields' => ['help' => $this->translator->trans('Titre teaser', [], 'admin')],
                    'placeholder_fields' => ['help' => $this->translator->trans('Saisissez un titre', [], 'admin')],
                ]);
            }

            $urls = new WidgetType\UrlsCollectionType($this->coreLocator);
            $urls->add($builder, ['display_seo' => true]);

            $dates = new WidgetType\PublicationDatesType($this->coreLocator);
            $dates->add($builder);

            if ($this->isLayoutUser) {
                $builder->add('customLayout', Type\CheckboxType::class, [
                    'required' => false,
                    'display' => 'button',
                    'color' => 'outline-info-darken-dark',
                    'label' => $this->translator->trans('Template personnalisé', [], 'admin'),
                    'attr' => [
                        'data-config' => true,
                        'class' => 'w-100',
                        'group' => 'col-md-3 d-flex align-items-end',
                    ],
                ]);
            }
        }

        if ($isNew) {
            $save = new WidgetType\SubmitType($this->coreLocator);
            $save->add($builder);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Making::class,
            'website' => null,
            'translation_domain' => 'admin',
        ]);
    }
}
