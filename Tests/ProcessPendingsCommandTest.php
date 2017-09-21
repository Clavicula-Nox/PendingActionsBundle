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
    const ACTION_GROUP = "command_fake_group";

    /**
     * @return KernelInterface
     */
    private function getKernel($options = [])
    {
        return $this->bootKernel($options);
    }

    public function testCommand()
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $serviceAction = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            PendingAction::TYPE_SERVICE,
            ServiceHandlerTest::$params,
            ProcessPendingsCommandTest::ACTION_GROUP
        );
        $serviceActionId = $serviceAction->getId();

        $eventAction = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            PendingAction::TYPE_EVENT,
            EventHandlerTest::$params,
            ProcessPendingsCommandTest::ACTION_GROUP
        );
        $eventActionId = $eventAction->getId();

        $commandAction = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions_service")->register(
            PendingAction::TYPE_COMMAND,
            CommandHandlerTest::$params,
            ProcessPendingsCommandTest::ACTION_GROUP
        );
        $commandActionId = $commandAction->getId();

        $application->add(new ProcessPendingsCommand());

        $command = $application->find('cn:pending-actions:process');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'actionGroup' => ProcessPendingsCommandTest::ACTION_GROUP,
        ));

        $output = $commandTester->getDisplay();
        $serviceAction = $this->getKernel()->getContainer()->get("doctrine")->getRepository("PendingActionsBundle:PendingAction")->find($serviceActionId);
        $eventAction = $this->getKernel()->getContainer()->get("doctrine")->getRepository("PendingActionsBundle:PendingAction")->find($eventActionId);
        $commandAction = $this->getKernel()->getContainer()->get("doctrine")->getRepository("PendingActionsBundle:PendingAction")->find($commandActionId);

        $this->assertContains("Action " . $serviceActionId . " : Processed", $output);
        $this->assertContains("Action " . $eventActionId . " : Processed", $output);
        $this->assertContains("Action " . $commandActionId . " : Processed", $output);
        $this->assertEquals(PendingAction::STATE_PROCESSED, $serviceAction->getState());
        $this->assertEquals(PendingAction::STATE_PROCESSED, $eventAction->getState());
        $this->assertEquals(PendingAction::STATE_PROCESSED, $commandAction->getState());
    }
}
