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
 * Class FakeException
 * @package ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes
 */
class FakeException extends \Exception
{
    const FAKE_MESSAGE = "Fake Exception Raised";
}
