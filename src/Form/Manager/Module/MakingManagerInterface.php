<?php

namespace App\Form\Manager\Module;

use App\Entity\Core\Website;
use App\Entity\Module\Making\Making;

/**
 * MakingManagerInterface.
 *
 * @author Sébastien FOURNIER <contact@sebastien-fournier.com>
 */
interface MakingManagerInterface
{
    public function prePersist(Making $making, Website $website): void;
}