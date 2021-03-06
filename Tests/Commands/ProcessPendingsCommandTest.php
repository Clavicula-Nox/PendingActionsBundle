<?php

/*
* This file is part of the PendingActionsBundle.
*
* (c) Adrien Lochon <adrien@claviculanox.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace ClaviculaNox\PendingActionsBundle\Tests\Commands;

use ClaviculaNox\PendingActionsBundle\Command\ProcessPendingsCommand;
use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeService;
use ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Command\FakeCommand;
use ClaviculaNox\PendingActionsBundle\Tests\Handlers\CommandHandlerTest;
use ClaviculaNox\PendingActionsBundle\Tests\Handlers\EventHandlerTest;
use ClaviculaNox\PendingActionsBundle\Tests\Handlers\ServiceHandlerTest;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ProcessPendingsCommandTest.
 */
class ProcessPendingsCommandTest extends KernelTestCase
{
    /* @var string */
    public static $group = 'commandFakeGroup';

    /* @var array */
    public static $params = ['command' => 'fake:command',
        'arguments' => [
            'argA' => 'argValA',
            'argB' => 'argValB',
        ],
        'options' => [
            'optionA' => 'optionValA',
            'optionB' => 'optionValB',
            'optionC' => 'optionValC',
        ],
    ];

    /**
     * @param array $options
     *
     * @return KernelInterface
     */
    private function getKernel($options = []): KernelInterface
    {
        return $this->bootKernel($options);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testCommand(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $tests = [
            [
                'handler' => ServiceHandlerTest::$handlerDefault,
                'params' => ServiceHandlerTest::$params,
                'finalState' => PendingAction::STATE_PROCESSED,
                'output' => 'Processed',
            ],
            [
                'handler' => EventHandlerTest::$handlerDefault,
                'params' => EventHandlerTest::$params,
                'finalState' => PendingAction::STATE_PROCESSED,
                'output' => 'Processed',
            ],
            [
                'handler' => CommandHandlerTest::$handlerDefault,
                'params' => CommandHandlerTest::$params,
                'finalState' => PendingAction::STATE_PROCESSED,
                'output' => 'Processed',
            ],
            [
                'handler' => CommandHandlerTest::$handlerDefault, //$noCommandId
                'params' => [],
                'finalState' => PendingAction::STATE_ERROR,
                'output' => 'Error',
            ],
            [
                'handler' => CommandHandlerTest::$handlerDefault, //$noArgumentsId
                'params' => ['command' => 'fake:command'],
                'finalState' => PendingAction::STATE_ERROR,
                'output' => 'Error',
            ],
            [
                'handler' => CommandHandlerTest::$handlerDefault, //$noOptionsId
                'params' => [
                    'command' => 'fake:command',
                    'arguments' => [
                        'argA' => 'argValA',
                        'argB' => 'argValB',
                    ],
                ],
                'finalState' => PendingAction::STATE_ERROR,
                'output' => 'Error',
            ],
            [
                'handler' => CommandHandlerTest::$handlerDefault, //$noOptionsId
                'params' => [
                    'command' => 'fake:command',
                    'arguments' => [
                        'argB' => 'argValB',
                    ],
                ],
                'finalState' => PendingAction::STATE_ERROR,
                'output' => 'Error',
            ],
            [
                'handler' => 'FakeHandlerReallyFake', //$UnknownHandlerErrorId
                'params' => [],
                'finalState' => PendingAction::STATE_ERROR,
                'output' => 'Error',
            ],
            [
                'handler' => 'FakeHandler', //$NoInterfaceHandlerId
                'params' => [],
                'finalState' => PendingAction::STATE_HANDLER_ERROR,
                'output' => 'Handler Error',
            ],
            [
                'handler' => CommandHandlerTest::$handlerDefault, //$wrongCommandId
                'params' => [
                    'command' => 'fake:command:reallyfake',
                    'arguments' => [
                        'argA' => 'argValA',
                        'argB' => 'argValB',
                    ],
                    'options' => [
                        'optionA' => 'optionValA',
                        'optionB' => 'optionValB',
                        'optionC' => 'optionValC',
                    ],
                ],
                'finalState' => PendingAction::STATE_ERROR,
                'output' => 'Error',
            ],
            [
                'handler' => CommandHandlerTest::$handlerDefault, //$tooManyArgumentsId
                'params' => [
                    'command' => 'fake:command',
                    'arguments' => [
                        'argA' => 'argValA',
                        'argB' => 'argValB',
                        'argC' => 'argValC',
                    ],
                    'options' => [
                        'optionA' => 'optionValA',
                        'optionB' => 'optionValB',
                        'optionC' => 'optionValC',
                    ],
                ],
                'finalState' => PendingAction::STATE_ERROR,
                'output' => 'Error',
            ],
            [
                'handler' => CommandHandlerTest::$handlerDefault, //$tooManyOptionsId
                'params' => [
                    'command' => 'fake:command',
                    'arguments' => [
                        'argA' => 'argValA',
                        'argB' => 'argValB',
                    ],
                    'options' => [
                        'optionA' => 'optionValA',
                        'optionB' => 'optionValB',
                        'optionC' => 'optionValC',
                        'optionD' => 'optionValD',
                    ],
                ],
                'finalState' => PendingAction::STATE_ERROR,
                'output' => 'Error',
            ],
            [
                'handler' => ServiceHandlerTest::$handlerDefault,
                'params' => [
                    'serviceId' => 'fake.service',
                    'args' => array(
                        'argA' => FakeService::ARG_A,
                        'argB' => FakeService::ARG_B,
                    ),
                ],
                'finalState' => PendingAction::STATE_ERROR,
                'output' => 'Error',
            ],
            [
                'handler' => 'TestHandler',
                'params' => [],
                'finalState' => PendingAction::STATE_ERROR,
                'output' => 'Error',
            ],
        ];

        foreach ($tests as $key => $test) {
            try {
                /* @var $action PendingAction */
                $action = $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->register(
                    $test['handler'],
                    $test['params'],
                    self::$group
                );
                $tests[$key]['actionId'] = $action->getId();
            } catch (\Exception $exception) {
                $this->assertEquals('The handler "FakeHandlerReallyFake" is not registered as a service.', $exception->getMessage());
                unset($tests[$key]);
            }
        }

        /* @var $alreadyRunningAction PendingAction */
        $alreadyRunningAction = $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->register(
            ServiceHandlerTest::$handlerDefault,
            ServiceHandlerTest::$params,
            self::$group
        );
        $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->setState($alreadyRunningAction, PendingAction::STATE_PROCESSING);
        $alreadyRunningActionId = $alreadyRunningAction->getId();

        $application->add(new ProcessPendingsCommand());
        $application->add(new FakeCommand());

        $command = $application->find('cn:pending-actions:process');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command' => $command->getName(),
            'actionGroup' => self::$group,
        ]);

        $output = $commandTester->getDisplay();
        foreach ($tests as $test) {
            /* @var $action PendingAction */
            $action = $this->getKernel()->getContainer()->get('doctrine')->getRepository('PendingActionsBundle:PendingAction')->find($test['actionId']);
            $this->assertContains('Action '.$action->getId().' : '.$test['output'], $output);
            $this->assertEquals($test['finalState'], $action->getState());
        }

        $alreadyRunningAction = $this->getKernel()->getContainer()->get('doctrine')->getRepository('PendingActionsBundle:PendingAction')->find($alreadyRunningActionId);
        $this->assertEquals(PendingAction::STATE_PROCESSING, $alreadyRunningAction->getState());

        $commandTester->execute(array(
            'command' => $command->getName(),
            'actionGroup' => self::$group,
        ));
        $output = $commandTester->getDisplay();
        $this->assertContains('No actions to process...', $output);
    }
}
