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
 * Class CommandHandlerTest
 * @package ClaviculaNox\PendingActionsBundle\Tests
 */
class CommandHandlerTest extends WebTestCase
{
    /* @var array */
    public static $params = ["command" => "fake:command",
        "arguments" => [
            "argA" => "argValA",
            "argB" => "argValB"
        ],
        "options" => [
            "optionA" => "optionValA",
            "optionB" => "optionValB",
            "optionC" => "optionValC"
        ]
    ];

    /* @var string */
    public static $handlerDefault = "CommandHandler";
    /* @var string */
    public static $handlerConfig = "CommandHandlerConfig";
    /* @var string */
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
            CommandHandlerTest::$params,
            CommandHandlerTest::$group
        );
    }

    public function testRegistrationDefault(): void
    {
        $Action = $this->getPendingAction(CommandHandlerTest::$handlerDefault);

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $Action);
    }

    public function testRegistrationConfig(): void
    {
        $Action = $this->getPendingAction(CommandHandlerTest::$handlerConfig);

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $Action);
    }

    public function testHandlerDefault(): void
    {
        $Action = $this->getPendingAction(CommandHandlerTest::$handlerDefault);

        $this->assertEquals(CommandHandlerTest::$handlerDefault, $Action->getHandler());
    }

    public function testHandlerConfig(): void
    {
        $Action = $this->getPendingAction(CommandHandlerTest::$handlerConfig);

        $this->assertEquals(CommandHandlerTest::$handlerConfig, $Action->getHandler());
    }

    public function testGroup(): void
    {
        $Action = $this->getPendingAction(CommandHandlerTest::$handlerDefault);

        $this->assertEquals(CommandHandlerTest::$group, $Action->getActionGroup());
    }
}
