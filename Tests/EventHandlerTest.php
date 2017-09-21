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
use ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeEvent;
use ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class EventHandlerTest
 * @package ClaviculaNox\PendingActionsBundle\Tests
 */
class EventHandlerTest extends WebTestCase
{
    public static $params = [
        "eventClassName" => "\ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeEvent",
        "eventId" => "fake_event.fake_method",
        "args" => [
            "argA" => FakeEvent::ARG_A,
            "argB" => FakeEvent::ARG_B
        ]
    ];

    public static $paramsException = [
        "eventClassName" => "\ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeEvent",
        "eventId" => "fake_event.fake_method_exception",
        "args" => [
            "argA" => FakeEvent::ARG_A,
            "argB" => FakeEvent::ARG_B
        ]
    ];

    public static $group = "testGroup";

    /**
     * @return KernelInterface
     */
    private function getKernel($options = [])
    {
        return $this->bootKernel($options);
    }

    /**
     * @return PendingAction
     */
    private function getExceptionPendingAction()
    {
        return $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions.event_handler")->register(
            EventHandlerTest::$paramsException,
            EventHandlerTest::$group
        );
    }

    /**
     * @return PendingAction
     */
    private function getPendingAction()
    {
        return $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions.event_handler")->register(
            EventHandlerTest::$params,
            EventHandlerTest::$group
        );
    }

    public function testRegistration()
    {
        $Action = $this->getPendingAction();

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $Action);
    }

    public function testGroup()
    {
        $Action = $this->getPendingAction();

        $this->assertEquals(EventHandlerTest::$group, $Action->getActionGroup());
    }

    public function testPendingAction()
    {
        $Action = $this->getExceptionPendingAction();
        try {
            $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions.event_handler")->process($Action);
            $this->fail("Expected Exception has not been raised.");
        } catch (FakeException $e) {
            $this->assertEquals($e->getMessage(), FakeException::FAKE_MESSAGE);
        }

        $Action = $this->getPendingAction();

        $result = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions.event_handler")->process($Action);
        $this->assertEquals($result, PendingAction::STATE_PROCESSED);
    }
}
