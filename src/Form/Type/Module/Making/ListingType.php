<?php

namespace App\Form\Type\Module\Making;

use App\Entity\Module\Making\Category;
use App\Entity\Module\Making\Listing;
use App\Form\Widget as WidgetType;
use App\Service\Interface\CoreLocatorInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * ListingType.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class ListingType extends AbstractType
{
    private TranslatorInterface $translator;
    private bool $isInternalUser;

    /**
     * ListingType constructor.
     */
    public function __construct(
        private readonly CoreLocatorInterface $coreLocator,
        private readonly TokenStorageInterface $tokenStorage
    ) {
        $this->translator = $this->coreLocator->translator();
        $user = !empty($this->tokenStorage->getToken()) ? $this->tokenStorage->getToken()->getUser() : null;
        $this->isInternalUser = $user && in_array('ROLE_INTERNAL', $user->getRoles());
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = !$builder->getData()->getId();

        $adminName = new WidgetType\AdminNameType($this->coreLocator);
        $adminName->add($builder, ['slug-internal' => $this->isInternalUser]);

        if (!$isNew) {
            $builder->add('categories', EntityType::class, [
                'required' => false,
                'display' => 'search',
                'label' => $this->translator->trans('Catégories', [], 'admin'),
                'attr' => [
                    'data-placeholder' => $this->translator->trans('Sélectionnez', [], 'admin'),
                ],
                'class' => Category::class,
                'choice_label' => function ($entity) {
                    return strip_tags($entity->getAdminName());
                },
                'multiple' => true,
            ]);
        }

        if (!$isNew && $this->isInternalUser) {
            $builder->add('orderBy', Type\ChoiceType::class, [
                'label' => $this->translator->trans('Ordonner les réalisations par', [], 'admin'),
                'display' => 'search',
                'attr' => ['group' => 'col-md-4', 'data-config' => true],
                'choices' => [
                    $this->translator->trans('Positions des catégories (croissantes)', [], 'admin') => 'category-position-asc',
                    $this->translator->trans('Positions des catégories (décroissantes)', [], 'admin') => 'category-position-desc',
                    $this->translator->trans('Titres des catégories (croissants)', [], 'admin') => 'category-title-asc',
                    $this->translator->trans('Titres des catégories (décroissants)', [], 'admin') => 'category-title-desc',
                    $this->translator->trans('Dates (croissantes)', [], 'admin') => 'publicationStart-asc',
                    $this->translator->trans('Dates (décroissantes)', [], 'admin') => 'publicationStart-desc',
                    $this->translator->trans('Positions (croissantes)', [], 'admin') => 'position-asc',
                    $this->translator->trans('Positions (décroissantes)', [], 'admin') => 'position-desc',
                ],
            ]);

            $builder->add('formatDate', WidgetType\FormatDateType::class, [
                'attr' => ['group' => 'col-md-4', 'data-config' => true],
            ]);

            $builder->add('itemsPerPage', Type\IntegerType::class, [
                'required' => false,
                'label' => $this->translator->trans('Nombre de réalisations par page', [], 'admin'),
                'attr' => ['group' => 'col-md-4', 'data-config' => true],
            ]);

            $builder->add('hideDate', Type\CheckboxType::class, [
                'required' => false,
                'display' => 'button',
                'color' => 'outline-info-darken-dark',
                'label' => $this->translator->trans('Cacher la date', [], 'admin'),
                'attr' => ['group' => 'col-md-3', 'class' => 'w-100', 'data-config' => true],
            ]);

            $builder->add('displayCategory', Type\CheckboxType::class, [
                'required' => false,
                'display' => 'button',
                'color' => 'outline-info-darken-dark',
                'label' => $this->translator->trans('Afficher le nom de la catégorie', [], 'admin'),
                'attr' => ['group' => 'col-md-3', 'class' => 'w-100', 'data-config' => true],
            ]);

            $builder->add('displayThumbnail', Type\CheckboxType::class, [
                'required' => false,
                'display' => 'button',
                'color' => 'outline-info-darken-dark',
                'label' => $this->translator->trans('Afficher les vignettes', [], 'admin'),
                'attr' => ['group' => 'col-md-3', 'class' => 'w-100', 'data-config' => true],
            ]);

            $builder->add('largeFirst', Type\CheckboxType::class, [
                'required' => false,
                'display' => 'button',
                'color' => 'outline-info-darken-dark',
                'label' => $this->translator->trans('Mettre en avant la dernière réalisation', [], 'admin'),
                'attr' => ['group' => 'col-md-3', 'class' => 'w-100', 'data-config' => true],
            ]);

            $builder->add('scrollInfinite', Type\CheckboxType::class, [
                'required' => false,
                'display' => 'button',
                'color' => 'outline-info-darken-dark',
                'label' => $this->translator->trans('Scroll infinite', [], 'admin'),
                'attr' => ['group' => 'col-md-3', 'class' => 'w-100', 'data-config' => true],
            ]);
        }

        $save = new WidgetType\SubmitType($this->coreLocator);
        $save->add($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Listing::class,
            'website' => null,
            'translation_domain' => 'admin',
        ]);
    }
}
