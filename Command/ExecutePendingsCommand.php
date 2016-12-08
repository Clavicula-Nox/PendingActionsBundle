<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Command;

use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ExecutePendingsCommand
 * @package CoreBundle\Command
 */
class ExecutePendingsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cn:pending-actions:execute')
            ->setDefinition(
                array(
                    new InputArgument('actionGroup', InputArgument::OPTIONAL, 'The action group')
                )
            )
            ->setHelp(<<<'EOT'
The <info>cn:pending-actions:execute</info> command executes the pending actions of an action group : 

  <info>php %command.full_name% my_group_name</info>

The actionGroup parameter is optional, if not set, it'll execute all pending actions.

EOT
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $output->write("Getting pending actions...", true);
        if (!is_null($input->getArgument('actionGroup'))) {
            $output->write("   Selected group : " . $input->getArgument('actionGroup'), true);
        }

        $pendingActions = $this->getContainer()
            ->get("cn_pending_actions.pending_actions_service")
            ->getPendingActions($input->getArgument('actionGroup'), true);
        $output->write("   Processing actions...", true);

        $counter = 1;
        $total = count($pendingActions);
        foreach ($pendingActions as $pendingAction)
        {
            $output->write("   Action " . $counter . "/" . $total, true);
            $counter++;
            $this->getContainer()->get("cn_pending_actions.pending_actions_service")->setState($pendingAction, PendingAction::STATE_PROCESSING);

            if (!$this->getContainer()->get("cn_pending_actions.pending_actions_service")->checkPendingAction($pendingAction)) {
                $this->getContainer()->get("cn_pending_actions.pending_actions_service")->setState($pendingAction, PendingAction::STATE_ERROR);
                continue;
            }

            /* @var $pendingAction PendingAction */
            switch ($pendingAction->getType())
            {
                case PendingAction::TYPE_SERVICE :
                {
                    $params = json_decode($pendingAction->getActionParams(), true);
                    call_user_func_array(array($this->getContainer()->get($params["serviceId"]), $params["method"]), $params['args']);
                    $this->getContainer()->get("cn_pending_actions.pending_actions_service")->setState($pendingAction, PendingAction::STATE_PROCESSED);
                    break;
                }

                default :
                {
                    $this->getContainer()->get("cn_pending_actions.pending_actions_service")->setState($pendingAction, PendingAction::STATE_ERROR);
                    break;
                }
            }
        }
    }
}
