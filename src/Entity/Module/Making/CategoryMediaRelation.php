<?php

namespace App\Entity\Module\Making;

use App\Entity\BaseMediaRelation;
use App\Repository\Module\Making\CategoryMediaRelationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryMediaRelation.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_making_category_media_relations')]
#[ORM\Entity(repositoryClass: CategoryMediaRelationRepository::class)]
class CategoryMediaRelation extends BaseMediaRelation
{
    #[ORM\ManyToOne(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'mediaRelations')]
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
