<?php

namespace App\Form\Type\Module\Making;

use App\Entity\Module\Making\Category;
use App\Form\Widget as WidgetType;
use App\Service\Interface\CoreLocatorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * CategoryType.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class CategoryType extends AbstractType
{
    private TranslatorInterface $translator;
    private EntityManagerInterface $entityManager;
    private bool $isInternalUser;

    /**
     * CategoryType constructor.
     */
    public function __construct(
        private readonly CoreLocatorInterface $coreLocator,
        private readonly TokenStorageInterface $tokenStorage
    ) {
        $this->translator = $this->coreLocator->translator();
        $this->entityManager = $this->coreLocator->em();
        $user = !empty($this->tokenStorage->getToken()) ? $this->tokenStorage->getToken()->getUser() : null;
        $this->isInternalUser = $user && in_array('ROLE_INTERNAL', $user->getRoles());
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $isNew = !$builder->getData()->getId();

        $adminName = new WidgetType\AdminNameType($this->coreLocator);
        $adminName->add($builder);

        if (!$isNew && $this->isInternalUser) {
            $builder->add('orderBy', Type\ChoiceType::class, [
                'label' => $this->translator->trans('Ordonner les réalisations par', [], 'admin'),
                'display' => 'search',
                'attr' => ['group' => 'col-md-4', 'data-config' => true],
                'choices' => [
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

            $builder->add('asDefault', Type\CheckboxType::class, [
                'required' => false,
                'display' => 'button',
                'color' => 'outline-info-darken-dark',
                'label' => $this->translator->trans('Catégorie principale', [], 'admin'),
                'attr' => ['group' => 'col-md-3', 'class' => 'w-100', 'data-config' => true],
            ]);

            $builder->add('mainMediaInHeader', Type\CheckboxType::class, [
                'required' => false,
                'display' => 'button',
                'color' => 'outline-info-darken-dark',
                'label' => $this->translator->trans("Afficher l'image principale dans les entêtes", [], 'admin'),
                'attr' => ['group' => 'col-md-3', 'class' => 'w-100', 'data-config' => true],
            ]);
        }

        if (!$isNew) {
            $mediaRelations = new WidgetType\MediaRelationsCollectionType($this->coreLocator);
            $mediaRelations->add($builder, [
                'data_config' => true,
                'entry_options' => ['onlyMedia' => true],
            ]);

            $i18ns = new WidgetType\i18nsCollectionType($this->coreLocator);
            $i18ns->add($builder, [
                'website' => $options['website'],
                'fields' => ['title' => 'col-md-8', 'targetLabel' => 'col-md-4'],
                'label_fields' => ['targetLabel' => $this->translator->trans('Label du lien de retour', [], 'admin')],
                'target_config' => false,
            ]);
        }

        $save = new WidgetType\SubmitType($this->coreLocator);
        $save->add($builder);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'website' => null,
            'translation_domain' => 'admin',
        ]);
    }
}
