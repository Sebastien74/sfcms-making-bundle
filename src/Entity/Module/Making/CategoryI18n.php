<?php

namespace App\Entity\Module\Making;

use App\Entity\BaseI18n;
use App\Repository\Module\Making\CategoryI18nRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryI18n.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_making_category_i18ns')]
#[ORM\Entity(repositoryClass: CategoryI18nRepository::class)]
class CategoryI18n extends BaseI18n
{
    #[ORM\ManyToOne(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'i18ns')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    private ?Category $category = null;

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }
}
