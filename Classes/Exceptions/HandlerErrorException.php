<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Classes\Exceptions;

/**
 * Class HandlerErrorException.
 */
class HandlerErrorException extends \Exception
{
    /**
     * HandlerErrorException constructor.
     *
     * @param string          $msg
     * @param \Exception|null $previous
     */
    public function __construct(string $msg, \Exception $previous = null)
    {
        parent::__construct($msg, 0, $previous);
    }
}
