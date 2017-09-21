<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes;

/**
 * Class FakeService
 * @package ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes
 */
class FakeService
{
    const ARG_A = "defaultMode";
    const ARG_B = "defaultTitle";

    /**
     * @param string $mode
     * @param string $title
     * @return bool
     */
    public function fakeMethod($argA, $argB)
    {
        return $argA == FakeService::ARG_A && $argB == FakeService::ARG_B;
    }
}
