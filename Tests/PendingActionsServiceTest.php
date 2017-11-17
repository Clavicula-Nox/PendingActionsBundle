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
 * Class PendingActionsServiceTest.
 */
class PendingActionsServiceTest extends WebTestCase
{
    public static $group = 'countGroup';
    public static $count = 5;

    /**
     * @return KernelInterface
     */
    private function getKernel($options = []): KernelInterface
    {
        return $this->bootKernel($options);
    }

    public function testGetPendingActions(): void
    {
        $actions = $this
            ->getKernel()
            ->getContainer()
            ->get('cn_pending_actions.pending_actions_service')->getPendingActions(self::$group);

        $this->assertEquals(0, count($actions));

        for ($i = 1; $i <= self::$count; ++$i) {
            $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->register(
                ServiceHandlerTest::$handlerDefault,
                ServiceHandlerTest::$params,
                self::$group
            );
        }

        $actions = $this
            ->getKernel()
            ->getContainer()
            ->get('cn_pending_actions.pending_actions_service')->getPendingActions(self::$group);

        $this->assertEquals(self::$count, count($actions));

        for ($i = 1; $i <= self::$count; ++$i) {
            $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->register(
                EventHandlerTest::$handlerDefault,
                EventHandlerTest::$params,
                self::$group
            );
        }

        $actions = $this
            ->getKernel()
            ->getContainer()
            ->get('cn_pending_actions.pending_actions_service')->getPendingActions(self::$group, true);

        $this->assertEquals(2, count($actions));
    }

    public function testRegisterPendingActionsAndStates(): void
    {
        $action = $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->register(
            ServiceHandlerTest::$handlerDefault,
            ServiceHandlerTest::$params,
            ServiceHandlerTest::$group
        );

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $action);

        $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->setState(
            $action,
            PendingAction::STATE_ERROR
        );

        $this->assertEquals(PendingAction::STATE_ERROR, $action->getState());
    }
}
