<?php

namespace App\Entity\Module\Making;

use App\Entity\BaseTeaser;
use App\Repository\Module\Making\TeaserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Teaser.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_making_teaser')]
#[ORM\Entity(repositoryClass: TeaserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\AssociationOverrides([
    new ORM\AssociationOverride(
        name: 'categories',
        joinColumns: [new ORM\JoinColumn(name: 'teaser_id', referencedColumnName: 'id', onDelete: 'cascade')],
        inverseJoinColumns: [new ORM\InverseJoinColumn(name: 'category_id', referencedColumnName: 'id', onDelete: 'cascade')],
        joinTable: new ORM\JoinTable(name: 'module_making_teaser_categories')
    ),
])]
class Teaser extends BaseTeaser
{
    /**
     * Configurations.
     */
    protected static array $interface = [
        'name' => 'makingteaser',
    ];

    #[ORM\OneToMany(mappedBy: 'teaser', targetEntity: TeaserI18n::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    #[ORM\OrderBy(['locale' => 'ASC'])]
    #[Assert\Valid]
    private ArrayCollection|PersistentCollection $i18ns;

    #[ORM\ManyToMany(targetEntity: Category::class, cascade: ['persist'])]
    #[ORM\OrderBy(['adminName' => 'ASC'])]
    #[Assert\Valid]
    private ArrayCollection|PersistentCollection $categories;

    /**
     * Teaser constructor.
     */
    public function __construct()
    {
        $this->i18ns = new ArrayCollection();
        $this->categories = new ArrayCollection();
    }

    /**
     * @return Collection<int, TeaserI18n>
     */
    public function getI18ns(): Collection
    {
        return $this->i18ns;
    }

    public function addI18n(TeaserI18n $i18n): static
    {
        if (!$this->i18ns->contains($i18n)) {
            $this->i18ns->add($i18n);
            $i18n->setTeaser($this);
        }

        return $this;
    }

    public function removeI18n(TeaserI18n $i18n): static
    {
        if ($this->i18ns->removeElement($i18n)) {
            // set the owning side to null (unless already changed)
            if ($i18n->getTeaser() === $this) {
                $i18n->setTeaser(null);
            }
        }

        return $this;
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
