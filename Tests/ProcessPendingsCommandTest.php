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

use ClaviculaNox\PendingActionsBundle\Command\ProcessPendingsCommand;
use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ProcessPendingsCommandTest
 * @package ClaviculaNox\PendingActionsBundle\Tests
 */
class ProcessPendingsCommandTest extends KernelTestCase
{
    /* @var string */
    public static $group = "commandFakeGroup";

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

    /**
     * @return KernelInterface
     */
    private function getKernel($options = []): KernelInterface
    {
        return $this->bootKernel($options);
    }

    public function testCommand(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $serviceAction = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            ServiceHandlerTest::$handlerDefault,
            ServiceHandlerTest::$params,
            ProcessPendingsCommandTest::$group
        );
        $serviceActionId = $serviceAction->getId();

        $eventAction = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            EventHandlerTest::$handlerDefault,
            EventHandlerTest::$params,
            ProcessPendingsCommandTest::$group
        );
        $eventActionId = $eventAction->getId();

        $commandAction = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            CommandHandlerTest::$handlerDefault,
            CommandHandlerTest::$params,
            ProcessPendingsCommandTest::$group
        );
        $commandActionId = $commandAction->getId();

        $PendingActionError = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            CommandHandlerTest::$handlerDefault,
            [],
            ProcessPendingsCommandTest::$group
        );
        $noCommandId = $PendingActionError->getId();

        $PendingActionError = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            CommandHandlerTest::$handlerDefault,
            ["command" => "fake:command"],
            ProcessPendingsCommandTest::$group
        );
        $noArgumentsId = $PendingActionError->getId();

        $PendingActionError = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            CommandHandlerTest::$handlerDefault,
            [
                "command" => "fake:command",
                "arguments" => [
                    "argA" => "argValA",
                    "argB" => "argValB"
                ]
            ],
            ProcessPendingsCommandTest::$group
        );
        $noOptionsId = $PendingActionError->getId();

        $PendingActionError = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            CommandHandlerTest::$handlerDefault,
            [
                "command" => "fake:command:reallyfake",
                "arguments" => [
                    "argA" => "argValA",
                    "argB" => "argValB"
                ],
                "options" => [
                    "optionA" => "optionValA",
                    "optionB" => "optionValB",
                    "optionC" => "optionValC"
                ]
            ],
            ProcessPendingsCommandTest::$group
        );
        $wrongCommandId = $PendingActionError->getId();

        $PendingActionError = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            CommandHandlerTest::$handlerDefault,
            [
                "command" => "fake:command",
                "arguments" => [
                    "argA" => "argValA",
                    "argB" => "argValB",
                    "argC" => "argValC"
                ],
                "options" => [
                    "optionA" => "optionValA",
                    "optionB" => "optionValB",
                    "optionC" => "optionValC"
                ]
            ],
            ProcessPendingsCommandTest::$group
        );
        $tooManyArgumentsId = $PendingActionError->getId();

        $PendingActionError = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            CommandHandlerTest::$handlerDefault,
            [
                "command" => "fake:command",
                "arguments" => [
                    "argA" => "argValA",
                    "argB" => "argValB"
                ],
                "options" => [
                    "optionA" => "optionValA",
                    "optionB" => "optionValB",
                    "optionC" => "optionValC",
                    "optionD" => "optionValD"
                ]
            ],
            ProcessPendingsCommandTest::$group
        );
        $tooManyOptionsId = $PendingActionError->getId();

        $alreadyRunningAction = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            ServiceHandlerTest::$handlerDefault,
            ServiceHandlerTest::$params,
            ProcessPendingsCommandTest::$group
        );
        $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->setState($alreadyRunningAction, PendingAction::STATE_PROCESSING);
        $alreadyRunningActionId = $alreadyRunningAction->getId();

        $application->add(new ProcessPendingsCommand());

        $command = $application->find('cn:pending-actions:process');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'actionGroup' => ProcessPendingsCommandTest::$group,
        ));

        $output = $commandTester->getDisplay();
        $serviceAction = $this->getKernel()->getContainer()->get("doctrine")->getRepository("PendingActionsBundle:PendingAction")->find($serviceActionId);
        $eventAction = $this->getKernel()->getContainer()->get("doctrine")->getRepository("PendingActionsBundle:PendingAction")->find($eventActionId);
        $commandAction = $this->getKernel()->getContainer()->get("doctrine")->getRepository("PendingActionsBundle:PendingAction")->find($commandActionId);
        $PendingActionError = $this->getKernel()->getContainer()->get("doctrine")->getRepository("PendingActionsBundle:PendingAction")->find($tooManyOptionsId);
        $alreadyRunningAction = $this->getKernel()->getContainer()->get("doctrine")->getRepository("PendingActionsBundle:PendingAction")->find($alreadyRunningActionId);

        $this->assertContains("Action " . $serviceActionId . " : Processed", $output);
        $this->assertContains("Action " . $eventActionId . " : Processed", $output);
        $this->assertContains("Action " . $commandActionId . " : Processed", $output);
        $this->assertEquals(PendingAction::STATE_PROCESSED, $serviceAction->getState());
        $this->assertEquals(PendingAction::STATE_PROCESSED, $eventAction->getState());
        $this->assertEquals(PendingAction::STATE_PROCESSED, $commandAction->getState());
        $this->assertEquals(PendingAction::STATE_PROCESSING, $alreadyRunningAction->getState());

        $this->assertContains("Action " . $noCommandId . " : Error", $output);
        $this->assertContains("Action " . $noArgumentsId . " : Error", $output);
        $this->assertContains("Action " . $noOptionsId . " : Error", $output);
        $this->assertContains("Action " . $wrongCommandId . " : Error", $output);
        $this->assertContains("Action " . $tooManyArgumentsId . " : Error", $output);
        $this->assertContains("Action " . $tooManyOptionsId . " : Error", $output);
        $this->assertEquals(PendingAction::STATE_ERROR, $PendingActionError->getState());

        $commandTester->execute(array(
            'command'  => $command->getName(),
            'actionGroup' => ProcessPendingsCommandTest::$group,
        ));
        $output = $commandTester->getDisplay();
        $this->assertContains("No actions to process...", $output);
    }
}
