<?php

namespace App\Entity\Module\Making;

use App\Entity\BaseMediaRelation;
use App\Repository\Module\Making\MakingMediaRelationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * MakingMediaRelation.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[ORM\Table(name: 'module_making_media_relations')]
#[ORM\Entity(repositoryClass: MakingMediaRelationRepository::class)]
class MakingMediaRelation extends BaseMediaRelation
{
    #[ORM\ManyToOne(targetEntity: Making::class, cascade: ['persist'], inversedBy: 'mediaRelations')]
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
