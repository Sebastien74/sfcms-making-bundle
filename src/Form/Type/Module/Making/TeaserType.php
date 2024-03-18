<?php

namespace App\Form\Type\Module\Making;

use App\Entity\Module\Making\Category;
use App\Entity\Module\Making\Teaser;
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
 * TeaserType.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class TeaserType extends AbstractType
{
    private TranslatorInterface $translator;
    private bool $isInternalUser;

    /**
     * TeaserType constructor.
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

        $adminNameGroup = 'col-12';
        if (!$isNew && $this->isInternalUser) {
            $adminNameGroup = 'col-md-4';
        } elseif (!$isNew) {
            $adminNameGroup = 'col-md-6';
        }

        $adminName = new WidgetType\AdminNameType($this->coreLocator);
        $adminName->add($builder, [
            'adminNameGroup' => $adminNameGroup,
            'slugGroup' => 'col-sm-2',
            'slug-internal' => $this->isInternalUser,
        ]);

        if (!$isNew) {
            if ($this->isInternalUser) {
                $builder->add('nbrItems', Type\IntegerType::class, [
                    'label' => $this->translator->trans('Nombre de réalisations par teaser', [], 'admin'),
                    'attr' => [
                        'placeholder' => $this->translator->trans('Saisissez un chiffre', [], 'admin'),
                        'group' => 'col-md-4',
                        'data-config' => true,
                    ],
                ]);

                $builder->add('itemsPerSlide', Type\IntegerType::class, [
                    'required' => false,
                    'label' => $this->translator->trans('Nombre de réalisations par slide', [], 'admin'),
                    'attr' => [
                        'placeholder' => $this->translator->trans('Saisissez un chiffre', [], 'admin'),
                        'group' => 'col-md-4',
                        'data-config' => true,
                    ],
                ]);

                $builder->add('orderBy', Type\ChoiceType::class, [
                    'label' => $this->translator->trans('Ordonner les réalisations par', [], 'admin'),
                    'display' => 'search',
                    'attr' => ['group' => 'col-md-4', 'data-config' => true],
                    'choices' => [
                        $this->translator->trans('Dates (croissantes)', [], 'admin') => 'publicationStart-asc',
                        $this->translator->trans('Dates (décroissantes)', [], 'admin') => 'publicationStart-desc',
                        $this->translator->trans('Catégories (croissantes)', [], 'admin') => 'category-asc',
                        $this->translator->trans('Catégories (décroissantes)', [], 'admin') => 'category-desc',
                        $this->translator->trans('Positions (croissantes)', [], 'admin') => 'position-asc',
                        $this->translator->trans('Positions (décroissantes)', [], 'admin') => 'position-desc',
                    ],
                ]);

                $builder->add('asSlider', Type\CheckboxType::class, [
                    'required' => false,
                    'display' => 'button',
                    'color' => 'outline-info-darken-dark',
                    'label' => $this->translator->trans('Afficher un slider', [], 'admin'),
                    'attr' => ['group' => 'col-md-4', 'class' => 'w-100', 'data-config' => true],
                ]);

                $builder->add('promote', Type\CheckboxType::class, [
                    'required' => false,
                    'display' => 'button',
                    'color' => 'outline-info-darken-dark',
                    'label' => $this->translator->trans('Afficher uniquement les réalisations mis en avant', [], 'admin'),
                    'attr' => ['group' => 'col-md-5', 'class' => 'w-100', 'data-config' => true],
                ]);
            }

            $builder->add('categories', EntityType::class, [
                'label' => $this->translator->trans('Catégories', [], 'admin'),
                'required' => false,
                'display' => 'search',
                'class' => Category::class,
                'attr' => [
                    'group' => !$isNew ? 'col-md-6' : 'col-12',
                    'data-placeholder' => $this->translator->trans('Sélectionnez', [], 'admin'),
                ],
                'choice_label' => function ($entity) {
                    return strip_tags($entity->getAdminName());
                },
                'multiple' => true,
            ]);

            $i18ns = new WidgetType\i18nsCollectionType($this->coreLocator);
            $i18ns->add($builder, [
                'website' => $options['website'],
                'fields' => ['title'],
                'title_force' => true,
            ]);
        }

        $save = new WidgetType\SubmitType($this->coreLocator);
        $save->add($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Teaser::class,
            'website' => null,
            'translation_domain' => 'admin',
        ]);
    }
}
