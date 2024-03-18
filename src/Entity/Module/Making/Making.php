<?php

namespace App\Entity\Module\Making;

use App\Entity\BaseEntity;
use App\Entity\Core\Website;
use App\Entity\Layout\Layout;
use App\Entity\Seo\Url;
use App\Repository\Module\Making\MakingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Making.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_making')]
#[ORM\Entity(repositoryClass: MakingRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\AssociationOverrides([
    new ORM\AssociationOverride(
        name: 'urls',
        joinColumns: [new ORM\JoinColumn(name: 'making_id', referencedColumnName: 'id', onDelete: 'cascade')],
        inverseJoinColumns: [new ORM\InverseJoinColumn(name: 'url_id', referencedColumnName: 'id', unique: true, onDelete: 'cascade')],
        joinTable: new ORM\JoinTable(name: 'module_making_urls')
    ),
])]
class Making extends BaseEntity
{
    /**
     * Configurations.
     */
    protected static string $masterField = 'website';
    protected static array $interface = [
        'name' => 'making',
        'card' => true,
        'search' => true,
        'resize' => true,
        'indexPage' => 'category',
        'listingClass' => Listing::class,
        'seo' => [
            'i18ns.title',
            'i18ns.introduction',
            'i18ns.body',
        ],
    ];
    protected static array $labels = [
        'i18ns.title' => 'Titre',
        'i18ns.introduction' => 'Introduction',
        'i18ns.body' => 'Description',
    ];

    #[ORM\Column(type: 'boolean')]
    private bool $promote = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $publicationStart = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $publicationEnd = null;

    #[ORM\Column(type: 'boolean')]
    private bool $customLayout = false;

    #[ORM\OneToOne(targetEntity: Layout::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'layout_id', referencedColumnName: 'id')]
    private ?Layout $layout = null;

    #[ORM\OneToMany(mappedBy: 'making', targetEntity: MakingI18n::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    #[ORM\OrderBy(['locale' => 'ASC'])]
    #[Assert\Valid]
    private ArrayCollection|PersistentCollection $i18ns;

    #[ORM\OneToMany(mappedBy: 'making', targetEntity: MakingMediaRelation::class, cascade: ['persist'], orphanRemoval: true)]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    #[ORM\OrderBy(['locale' => 'ASC'])]
    #[Assert\Valid]
    private ArrayCollection|PersistentCollection $mediaRelations;

    #[ORM\ManyToOne(targetEntity: Category::class, cascade: ['persist'], inversedBy: 'makings')]
    #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Assert\Valid]
    private ?Category $category = null;

    #[ORM\ManyToOne(targetEntity: Website::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Website $website = null;

    #[ORM\ManyToMany(targetEntity: Url::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['locale' => 'ASC'])]
    #[Assert\Valid]
    private ArrayCollection|PersistentCollection $urls;

    /**
     * Making constructor.
     */
    public function __construct()
    {
        $this->i18ns = new ArrayCollection();
        $this->mediaRelations = new ArrayCollection();
        $this->urls = new ArrayCollection();
    }

    public function isPromote(): ?bool
    {
        return $this->promote;
    }

    public function setPromote(bool $promote): self
    {
        $this->promote = $promote;

        return $this;
    }

    public function getPublicationStart(): ?\DateTimeInterface
    {
        return $this->publicationStart;
    }

    public function setPublicationStart(?\DateTimeInterface $publicationStart): self
    {
        $this->publicationStart = $publicationStart;

        return $this;
    }

    public function getPublicationEnd(): ?\DateTimeInterface
    {
        return $this->publicationEnd;
    }

    public function setPublicationEnd(?\DateTimeInterface $publicationEnd): self
    {
        $this->publicationEnd = $publicationEnd;

        return $this;
    }

    public function isCustomLayout(): ?bool
    {
        return $this->customLayout;
    }

    public function setCustomLayout(bool $customLayout): self
    {
        $this->customLayout = $customLayout;

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

    /**
     * @return Collection<int, MakingI18n>
     */
    public function getI18ns(): Collection
    {
        return $this->i18ns;
    }

    public function addI18n(MakingI18n $i18n): static
    {
        if (!$this->i18ns->contains($i18n)) {
            $this->i18ns->add($i18n);
            $i18n->setMaking($this);
        }

        return $this;
    }

    public function removeI18n(MakingI18n $i18n): static
    {
        if ($this->i18ns->removeElement($i18n)) {
            // set the owning side to null (unless already changed)
            if ($i18n->getMaking() === $this) {
                $i18n->setMaking(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MakingMediaRelation>
     */
    public function getMediaRelations(): Collection
    {
        return $this->mediaRelations;
    }

    public function addMediaRelation(MakingMediaRelation $mediaRelation): static
    {
        if (!$this->mediaRelations->contains($mediaRelation)) {
            $this->mediaRelations->add($mediaRelation);
            $mediaRelation->setMaking($this);
        }

        return $this;
    }

    public function removeMediaRelation(MakingMediaRelation $mediaRelation): static
    {
        if ($this->mediaRelations->removeElement($mediaRelation)) {
            // set the owning side to null (unless already changed)
            if ($mediaRelation->getMaking() === $this) {
                $mediaRelation->setMaking(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

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
     * @return Collection<int, Url>
     */
    public function getUrls(): Collection
    {
        return $this->urls;
    }

    public function addUrl(Url $url): self
    {
        if (!$this->urls->contains($url)) {
            $this->urls->add($url);
        }

        return $this;
    }

    public function removeUrl(Url $url): self
    {
        $this->urls->removeElement($url);

        return $this;
    }
}
