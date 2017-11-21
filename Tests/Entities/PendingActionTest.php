<?php

/*
* This file is part of the PendingActionsBundle.
*
* (c) Adrien Lochon <adrien@claviculanox.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace ClaviculaNox\PendingActionsBundle\Tests\Entities;

use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use ClaviculaNox\PendingActionsBundle\Tests\Handlers\CommandHandlerTest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class PendingActionTest.
 */
class PendingActionTest extends WebTestCase
{
    /**
     * @return KernelInterface
     */
    private function getKernel($options = []): KernelInterface
    {
        return $this->bootKernel($options);
    }

    /**
     * @return PendingAction
     */
    private function getPendingAction($handler): PendingAction
    {
        return $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->register(
            $handler,
            CommandHandlerTest::$params,
            CommandHandlerTest::$group
        );
    }

    public function testState(): void
    {
        $Action = $this->getPendingAction(CommandHandlerTest::$handlerDefault);

        $this->assertEquals(PendingAction::$labels[$Action->getState()], $Action->getStateLabel());
        $this->assertEquals($Action->getCreated()->format('Ymd'), $Action->getUpdated()->format('Ymd'));
    }
}
