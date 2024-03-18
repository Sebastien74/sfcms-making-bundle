<?php

namespace App\Entity\Module\Making;

use App\Entity\BaseEntity;
use App\Entity\Core\Website;
use App\Entity\Layout\Layout;
use App\Repository\Module\Making\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Category.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_making_category')]
#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Category extends BaseEntity
{
    /**
     * Configurations.
     */
    protected static string $masterField = 'website';
    protected static array $interface = [
        'name' => 'makingcategory',
        'resize' => true,
    ];

    #[ORM\Column(type: 'boolean')]
    private bool $asDefault = false;

    #[ORM\Column(type: 'boolean')]
    private bool $mainMediaInHeader = true;

    #[ORM\Column(type: 'boolean')]
    private bool $hideDate = false;

    #[ORM\Column(type: 'boolean')]
    private bool $displayCategory = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $formatDate = 'dd/MM';

    #[ORM\Column(type: 'integer', nullable: true)]
    #[Assert\NotBlank]
    private int $itemsPerPage = 12;

    #[ORM\Column(type: 'string', length: 255)]
    private string $orderBy = 'publicationStart-desc';

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: Making::class, cascade: ['persist'])]
    #[ORM\OrderBy(['publicationStart' => 'DESC'])]
    #[Assert\Valid]
    private ArrayCollection|PersistentCollection $makings;

    #[ORM\OneToOne(targetEntity: Layout::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'layout_id', referencedColumnName: 'id')]
    private ?Layout $layout = null;

    #[ORM\ManyToOne(targetEntity: Website::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Website $website = null;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: CategoryI18n::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    #[ORM\OrderBy(['locale' => 'ASC'])]
    #[Assert\Valid]
    private ArrayCollection|PersistentCollection $i18ns;

    #[ORM\OneToMany(mappedBy: 'category', targetEntity: CategoryMediaRelation::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    #[ORM\OrderBy(['locale' => 'ASC'])]
    #[Assert\Valid]
    private ArrayCollection|PersistentCollection $mediaRelations;

    /**
     * Category constructor.
     */
    public function __construct()
    {
        $this->makings = new ArrayCollection();
        $this->i18ns = new ArrayCollection();
        $this->mediaRelations = new ArrayCollection();
    }

    public function isAsDefault(): ?bool
    {
        return $this->asDefault;
    }

    public function setAsDefault(bool $asDefault): self
    {
        $this->asDefault = $asDefault;

        return $this;
    }

    public function isMainMediaInHeader(): ?bool
    {
        return $this->mainMediaInHeader;
    }

    public function setMainMediaInHeader(bool $mainMediaInHeader): self
    {
        $this->mainMediaInHeader = $mainMediaInHeader;

        return $this;
    }

    public function isHideDate(): ?bool
    {
        return $this->hideDate;
    }

    public function setHideDate(bool $hideDate): self
    {
        $this->hideDate = $hideDate;

        return $this;
    }

    public function isDisplayCategory(): ?bool
    {
        return $this->displayCategory;
    }

    public function setDisplayCategory(bool $displayCategory): self
    {
        $this->displayCategory = $displayCategory;

        return $this;
    }

    public function getFormatDate(): ?string
    {
        return $this->formatDate;
    }

    public function setFormatDate(?string $formatDate): self
    {
        $this->formatDate = $formatDate;

        return $this;
    }

    public function getItemsPerPage(): ?int
    {
        return $this->itemsPerPage;
    }

    public function setItemsPerPage(?int $itemsPerPage): self
    {
        $this->itemsPerPage = $itemsPerPage;

        return $this;
    }

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function setOrderBy(string $orderBy): self
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    /**
     * @return Collection<int, Making>
     */
    public function getMakings(): Collection
    {
        return $this->makings;
    }

    public function addMaking(Making $making): self
    {
        if (!$this->makings->contains($making)) {
            $this->makings->add($making);
            $making->setCategory($this);
        }

        return $this;
    }

    public function removeMaking(Making $making): self
    {
        if ($this->makings->removeElement($making)) {
            // set the owning side to null (unless already changed)
            if ($making->getCategory() === $this) {
                $making->setCategory(null);
            }
        }

        return $this;
    }

    public function getLayout(): ?Layout
    {
        return $this->layout;
    }

    public function setLayout(?Layout $layout): self
    {
        $this->layout = $layout;

        return $this;
    }

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): self
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return Collection<int, CategoryI18n>
     */
    public function getI18ns(): Collection
    {
        return $this->i18ns;
    }

    public function addI18n(CategoryI18n $i18n): static
    {
        if (!$this->i18ns->contains($i18n)) {
            $this->i18ns->add($i18n);
            $i18n->setCategory($this);
        }

        return $this;
    }

    public function removeI18n(CategoryI18n $i18n): static
    {
        if ($this->i18ns->removeElement($i18n)) {
            // set the owning side to null (unless already changed)
            if ($i18n->getCategory() === $this) {
                $i18n->setCategory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CategoryMediaRelation>
     */
    public function getMediaRelations(): Collection
    {
        return $this->mediaRelations;
    }

    public function addMediaRelation(CategoryMediaRelation $mediaRelation): static
    {
        if (!$this->mediaRelations->contains($mediaRelation)) {
            $this->mediaRelations->add($mediaRelation);
            $mediaRelation->setCategory($this);
        }

        return $this;
    }

    public function removeMediaRelation(CategoryMediaRelation $mediaRelation): static
    {
        if ($this->mediaRelations->removeElement($mediaRelation)) {
            // set the owning side to null (unless already changed)
            if ($mediaRelation->getCategory() === $this) {
                $mediaRelation->setCategory(null);
            }
        }

        return $this;
    }
}
