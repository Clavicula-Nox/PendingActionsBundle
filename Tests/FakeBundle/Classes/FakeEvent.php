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

use Symfony\Component\EventDispatcher\Event;

/**
 * Class FakeEvent.
 */
class FakeEvent extends Event
{
    const ARG_A = 'a';

    const ARG_B = 'b';

    public $argA;

    public $argB;

    /**
     * FakeEvent constructor.
     * @param string $argA
     * @param string $argB
     */
    public function __construct(string $argA, string $argB)
    {
        $this->argA = $argA;
        $this->argB = $argB;
    }
}
