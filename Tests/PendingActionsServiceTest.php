<?php

/*
* This file is part of the PendingActionsBundle.
*
* (c) Adrien Lochon <adrien@claviculanox.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace ClaviculaNox\PendingActionsBundle\Tests;

use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class PendingActionsServiceTest
 * @package ClaviculaNox\PendingActionsBundle\Tests
 */
class PendingActionsServiceTest extends WebTestCase
{
    /**
     * @return KernelInterface
     */
    private function getKernel($options = [])
    {
        return $this->bootKernel($options);
    }

    public function testRegisterPendingActions()
    {
        /* @var $action PendingAction */
        $action = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            PendingAction::TYPE_SERVICE,
            ServiceHandlerTest::$params,
            ServiceHandlerTest::$group
        );

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $action);

        $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->setState(
            $action,
            PendingAction::STATE_ERROR
        );

        $this->assertEquals(PendingAction::STATE_ERROR, $action->getState());

        $action = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions.event_handler")->register(
            EventHandlerTest::$params,
            EventHandlerTest::$group
        );

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $action);

        $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->setState(
            $action,
            PendingAction::STATE_ERROR
        );

        $this->assertEquals(PendingAction::STATE_ERROR, $action->getState());
    }
}
