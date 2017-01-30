<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Classes\Services\CommandHandler;

use ClaviculaNox\PendingActionsBundle\Command\ProcessPendingsCommand;
use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandHandlerService
 * @package ClaviculaNox\PendingActionsBundle\Classes\Services\CommandHandler
 */
class CommandHandlerService
{
    protected $EntityManager;

    /**
     * ServiceHandlerService constructor.
     * @param EntityManager $EntityManager
     */
    public function __construct(EntityManager $EntityManager)
    {
        $this->EntityManager = $EntityManager;
    }

    /**
     * @param $type
     * @param array $params
     * @param null|string $group
     * @return PendingAction
     */
    public function register($params = array(), $group = null)
    {
        $PendingAction = new PendingAction();
        $PendingAction->setType(PendingAction::TYPE_COMMAND);
        $PendingAction->setActionParams($params);
        $PendingAction->setActionGroup($group);
        $PendingAction->setCreated(new \DateTime());
        $PendingAction->setUpdated(new \DateTime());
        $PendingAction->setState(PendingAction::STATE_WAITING);
        $this->EntityManager->persist($PendingAction);
        $this->EntityManager->flush();

        return $PendingAction;
    }

    /**
     * @param PendingAction $PendingAction
     * @param ProcessPendingsCommand $ProcessPendingsCommand
     * @return bool
     */
    private function checkPendingAction(PendingAction $PendingAction, ProcessPendingsCommand $ProcessPendingsCommand)
    {
        $params = json_decode($PendingAction->getActionParams(), true);
        if (is_null($params)) {
            return false;
        }

        if (!isset($params['command'])) {
            return false;
        }

        if (!$ProcessPendingsCommand->getApplication()->has($params['command'])) {
            return false;
        }

        if (!isset($params['arguments'])) {
            return false;
        }

        if (!isset($params['options'])) {
            return false;
        }

        $command = $ProcessPendingsCommand->getApplication()->find($params['command']);

        foreach ($command->getDefinition()->getArguments() as $argument)
        {
            if ($argument->isRequired() && !isset($params['arguments'][$argument->getName()])) {
                return false;
            }

            unset($params['arguments'][$argument->getName()]);
        }

        if (count($params['arguments']) > 0) {
            return false;
        }

        foreach ($command->getDefinition()->getOptions() as $option)
        {
            if (isset($params['options'][$option->getName()])) {
                unset($params['options'][$option->getName()]);
            }
        }

        if (count($params['options']) > 0) {
            return false;
        }

        return true;
    }

    /**
     * @param PendingAction $PendingAction
     * @param ProcessPendingsCommand $ProcessPendingsCommand
     * @param OutputInterface $output
     * @return int
     */
    public function process(PendingAction $PendingAction,
                            ProcessPendingsCommand $ProcessPendingsCommand,
                            OutputInterface $output)
    {
        if (!$this->checkPendingAction($PendingAction, $ProcessPendingsCommand)) {
            return PendingAction::STATE_ERROR;
        }

        $params = json_decode($PendingAction->getActionParams(), true);

        $command = $ProcessPendingsCommand->getApplication()->find($params['command']);
        $commandArgs = array(
            'command' => $params['command']
        );

        foreach ($params["arguments"] as $key => $argument)
        {
            $commandArgs[$key] = $argument;
        }

        foreach ($params["options"] as $key => $option)
        {
            $commandArgs["--" . $key] = $option;
        }

        $arrayInput = new ArrayInput($commandArgs);
        $command->run($arrayInput, $output);

        return PendingAction::STATE_PROCESSED;
    }
}
