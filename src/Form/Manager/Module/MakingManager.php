<?php

namespace App\Form\Manager\Module;

use App\Entity\Core\Website;
use App\Entity\Module\Making\Making;
use App\Form\Interface\CoreFormManagerInterface;
use Doctrine\ORM\Mapping\MappingException;

/**
 * MakingManager.
 *
 * Manage admin Making form
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
class MakingManager implements MakingManagerInterface
{
    /**
     * MakingManager constructor.
     */
    public function __construct(private readonly CoreFormManagerInterface $coreLocator)
    {
    }

    /**
     * @prePersist
     *
     * @throws MappingException
     */
    public function prePersist(Making $making, Website $website): void
    {
        $this->coreLocator->base()->prePersist($making, $website);
    }
}