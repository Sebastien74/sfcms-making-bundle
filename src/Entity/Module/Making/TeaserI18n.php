<?php

namespace App\Entity\Module\Making;

use App\Entity\BaseI18n;
use App\Repository\Module\Making\TeaserI18nRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * CategoryI18n.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_making_teaser_i18ns')]
#[ORM\Entity(repositoryClass: TeaserI18nRepository::class)]
class TeaserI18n extends BaseI18n
{
    #[ORM\ManyToOne(targetEntity: Teaser::class, cascade: ['persist'], inversedBy: 'i18ns')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    private ?Teaser $teaser = null;

    public function getTeaser(): ?Teaser
    {
        return $this->teaser;
    }

    public function setTeaser(?Teaser $teaser): static
    {
        $this->teaser = $teaser;

        return $this;
    }
}
