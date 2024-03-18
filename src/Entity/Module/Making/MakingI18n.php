<?php

namespace App\Entity\Module\Making;

use App\Entity\BaseI18n;
use App\Repository\Module\Making\MakingI18nRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * MakingI18n.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_making_i18ns')]
#[ORM\Entity(repositoryClass: MakingI18nRepository::class)]
class MakingI18n extends BaseI18n
{
    #[ORM\ManyToOne(targetEntity: Making::class, cascade: ['persist'], inversedBy: 'i18ns')]
    #[ORM\JoinColumn(onDelete: 'cascade')]
    private ?Making $making = null;

    public function getMaking(): ?Making
    {
        return $this->making;
    }

    public function setMaking(?Making $making): static
    {
        $this->making = $making;

        return $this;
    }
}
