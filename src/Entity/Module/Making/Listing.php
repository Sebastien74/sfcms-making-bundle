<?php

namespace App\Entity\Module\Making;

use App\Entity\BaseListing;
use App\Repository\Module\Making\ListingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Listing.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_making_listing')]
#[ORM\Entity(repositoryClass: ListingRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\AssociationOverrides([
    new ORM\AssociationOverride(
        name: 'categories',
        joinColumns: [new ORM\JoinColumn(name: 'listing_id', referencedColumnName: 'id', onDelete: 'cascade')],
        inverseJoinColumns: [new ORM\InverseJoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'cascade')],
        joinTable: new ORM\JoinTable(name: 'module_making_listing_categories')
    ),
])]
class Listing extends BaseListing
{
    /**
     * Configurations.
     */
    protected static array $interface = [
        'name' => 'makinglisting',
    ];

    #[ORM\ManyToMany(targetEntity: Category::class, cascade: ['persist'])]
    #[Assert\Valid]
    private ArrayCollection|PersistentCollection $categories;

    /**
     * Listing constructor.
     */
    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
