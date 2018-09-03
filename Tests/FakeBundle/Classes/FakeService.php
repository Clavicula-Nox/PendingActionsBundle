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
 * Class FakeService.
 */
class FakeService
{
    const ARG_A = 'defaultMode';

    const ARG_B = 'defaultTitle';

    /**
     * @param string $argA
     * @param string $argB
     *
     * @return bool
     */
    public function fakeMethod(string $argA, string $argB): bool
    {
        return self::ARG_A == $argA && self::ARG_B == $argB;
    }
}
