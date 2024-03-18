<?php

namespace App\Form\Manager\Module;

use App\Entity\Core\Website;
use App\Entity\Module\Making\Making;
use App\Form\Interface\CoreFormManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * MakingManager.
 *
 * Manage admin Making form
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
#[Autoconfigure(tags: [
    ['name' => MakingManager::class, 'key' => 'module_making_form_manager'],
])]
class MakingManager
{
    /**
     * MakingManager constructor.
     */
    public function __construct(private readonly CoreFormManagerInterface $coreLocator)
    {
    }

    /**
     * @prePersist
     */
    public function prePersist(Making $making, Website $website): void
    {
        $this->coreLocator->base()->prePersist($making, $website);
    }
}
