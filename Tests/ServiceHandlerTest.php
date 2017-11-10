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
use ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ServiceHandlerTest
 * @package ClaviculaNox\PendingActionsBundle\Tests
 */
class ServiceHandlerTest extends WebTestCase
{
    public static $params = [
        "serviceId" => "fake.service",
        "method" => "fakeMethod",
        "args" => array(
            "argA" => FakeService::ARG_A,
            "argB" => FakeService::ARG_B
        )
    ];

    public static $handlerDefault = "ServiceHandler";
    public static $handlerConfig = "ServiceHandlerConfig";
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
    private function getPendingAction($handler): PendingAction
    {
        return $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            $handler,
            ServiceHandlerTest::$params,
            ServiceHandlerTest::$group
        );
    }

    public function testRegistrationDefault(): void
    {
        $Action = $this->getPendingAction(ServiceHandlerTest::$handlerDefault);

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $Action);
    }

    public function testRegistrationConfig(): void
    {
        $Action = $this->getPendingAction(ServiceHandlerTest::$handlerConfig);

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $Action);
    }

    public function testHandlerDefault(): void
    {
        $Action = $this->getPendingAction(ServiceHandlerTest::$handlerDefault);

        $this->assertEquals(ServiceHandlerTest::$handlerDefault, $Action->getHandler());
    }

    public function testHandlerConfig(): void
    {
        $Action = $this->getPendingAction(ServiceHandlerTest::$handlerConfig);

        $this->assertEquals(ServiceHandlerTest::$handlerConfig, $Action->getHandler());
    }

    public function testGroup(): void
    {
        $Action = $this->getPendingAction(ServiceHandlerTest::$handlerDefault);

        $this->assertEquals(ServiceHandlerTest::$group, $Action->getActionGroup());
    }

    public function testPendingAction(): void
    {
        $Action = $this->getPendingAction(ServiceHandlerTest::$handlerDefault);
        $result = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->process($Action);
        $this->assertEquals($result, PendingAction::STATE_PROCESSED);
    }
}
