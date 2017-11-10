<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Classes\Handlers;

use ClaviculaNox\PendingActionsBundle\Classes\Interfaces\HandlersInterface;
use ClaviculaNox\PendingActionsBundle\Command\ProcessPendingsCommand;
use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CommandHandler
 * @package ClaviculaNox\PendingActionsBundle\Classes\Handlers
 */
class CommandHandler implements HandlersInterface
{
    /**
     * @param PendingAction $PendingAction
     * @param ProcessPendingsCommand $ProcessPendingsCommand
     * @return bool
     */
    public function checkPendingAction(PendingAction $PendingAction, ProcessPendingsCommand $ProcessPendingsCommand = null): bool
    {
        $params = json_decode($PendingAction->getActionParams(), true);
        if (
            is_null($ProcessPendingsCommand) ||
            is_null($params) ||
            !isset($params['command']) ||
            !$ProcessPendingsCommand->getApplication()->has($params['command']) ||
            !isset($params['arguments']) ||
            !isset($params['options'])
        ) {
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
                            ProcessPendingsCommand $ProcessPendingsCommand = null,
                            OutputInterface $output = null): int
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
