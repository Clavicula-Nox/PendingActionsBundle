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
    const MODE = "defaultMode";
    const TITLE = "defaultTitle";

    public function __construct()
    {

    }

    /**
     * @param string $mode
     * @param string $title
     * @return bool
     */
    public function fakeMethod($mode, $title)
    {
        if ($mode == FakeService::MODE && $title == FakeService::TITLE) {
            return true;
        }

        return false;
    }
}
