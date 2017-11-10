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

    public static $handlerDefault = "EventHandler";
    public static $handlerConfig = "EventHandlerConfig";
    public static $group = "testGroup";

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
    private function getExceptionPendingAction(): PendingAction
    {
        return $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            EventHandlerTest::$handlerDefault,
            EventHandlerTest::$paramsException,
            EventHandlerTest::$group
        );
    }

    /**
     * @return PendingAction
     */
    private function getPendingAction($handler): PendingAction
    {
        return $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            $handler,
            EventHandlerTest::$params,
            EventHandlerTest::$group
        );
    }

    public function testRegistrationDefault(): void
    {
        $Action = $this->getPendingAction(EventHandlerTest::$handlerDefault);

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $Action);
    }

    public function testRegistrationConfig(): void
    {
        $Action = $this->getPendingAction(EventHandlerTest::$handlerConfig);

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $Action);
    }

    public function testHandlerDefault(): void
    {
        $Action = $this->getPendingAction(EventHandlerTest::$handlerDefault);

        $this->assertEquals(EventHandlerTest::$handlerDefault, $Action->getHandler());
    }

    public function testHandlerConfig(): void
    {
        $Action = $this->getPendingAction(EventHandlerTest::$handlerConfig);

        $this->assertEquals(EventHandlerTest::$handlerConfig, $Action->getHandler());
    }

    public function testGroup(): void
    {
        $Action = $this->getPendingAction(EventHandlerTest::$handlerDefault);

        $this->assertEquals(EventHandlerTest::$group, $Action->getActionGroup());
    }

    public function testPendingAction(): void
    {
        $Action = $this->getExceptionPendingAction();
        try {
            $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->process($Action);
            $this->fail("Expected Exception has not been raised.");
        } catch (FakeException $e) {
            $this->assertEquals($e->getMessage(), FakeException::FAKE_MESSAGE);
        }

        $Action = $this->getPendingAction(EventHandlerTest::$handlerDefault);

        $result = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->process($Action);
        $this->assertEquals($result, PendingAction::STATE_PROCESSED);
    }
}
