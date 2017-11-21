<?php

/*
* This file is part of the PendingActionsBundle.
*
* (c) Adrien Lochon <adrien@claviculanox.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace ClaviculaNox\PendingActionsBundle\Tests\DependencyInjection;

use ClaviculaNox\PendingActionsBundle\DependencyInjection\PendingActionsExtension;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class PendingActionsExtensionTest.
 */
class PendingActionsExtensionTest extends KernelTestCase
{
    public static $injectionHandler = 'injectionHandler';
    public static $injectionHandlerValue = 'injectionHandlerValue';

    public function testInjection()
    {
        $builder = new ContainerBuilder();

        $ext = new PendingActionsExtension();

        $ext->load([
            'pending_actions' => [
                'handlers' => [self::$injectionHandler => self::$injectionHandler],
            ],
        ], $builder);

        $handlers = $builder->getParameter('pending_actions.handlers');
        $this->assertEquals($handlers[self::$injectionHandler], self::$injectionHandler);
        $this->assertEquals($handlers['ServiceHandler'], 'cn_pending_actions.pending_actions.service_handler');
        $this->assertEquals($handlers['EventHandler'], 'cn_pending_actions.pending_actions.event_handler');
        $this->assertEquals($handlers['CommandHandler'], 'cn_pending_actions.pending_actions.command_handler');
    }
}
