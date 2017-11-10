<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Classes\Interfaces;

use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;

/**
 * Interface HandlersInterface
 * @package ClaviculaNox\PendingActionsBundle\Classes\Interfaces
 */
interface HandlersInterface
{
    public function checkPendingAction(PendingAction $PendingAction) : bool;
    public function process(PendingAction $PendingAction): int;
}
