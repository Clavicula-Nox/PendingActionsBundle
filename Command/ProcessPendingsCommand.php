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
 * Class ProcessPendingsCommand
 * @package ClaviculaNox\PendingActionsBundle\Command
 */
class ProcessPendingsCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('cn:pending-actions:process')
            ->setDescription('Processing of Pending Actions')
            ->setDefinition(
                array(
                    new InputArgument('actionGroup', InputArgument::OPTIONAL, 'The action group')
                )
            )
            ->setHelp(<<<'EOT'
The <info>cn:pending-actions:process</info> command processes the pending actions of an action group : 

  <info>php %command.full_name% my_group_name</info>

The actionGroup parameter is optional, if not set, it'll process all pending actions.
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
        $total = count($pendingActions);

        if ($total > 0) {
            $output->write("Processing actions...", true);
        } else {
            $output->write("No actions to process...", true);
        }

        $counter = 1;
        
        foreach ($pendingActions as $pendingAction)
        {
            /* @var $pendingAction PendingAction */
            $pendingAction = $this->getContainer()->get("doctrine")->getRepository("PendingActionsBundle:PendingAction")->find($pendingAction->getId());
            $output->write("   Action " . $counter . "/" . $total, true);
            $counter++;

            if ($pendingAction->getState() != PendingAction::STATE_WAITING) {
                continue;
            }

            $this->getContainer()->get("cn_pending_actions.pending_actions_service")->setState($pendingAction, PendingAction::STATE_PROCESSING);

            switch ($pendingAction->getType())
            {
                case PendingAction::TYPE_SERVICE :
                {
                    $result = $this->getContainer()->get("cn_pending_actions.pending_actions.service_handler")->process($pendingAction);
                    break;
                }

                case PendingAction::TYPE_EVENT :
                {
                    $result = $this->getContainer()->get("cn_pending_actions.pending_actions.event_handler")->process($pendingAction);
                    break;
                }

                case PendingAction::TYPE_COMMAND :
                {
                    $result = $this->getContainer()->get("cn_pending_actions.pending_actions.command_handler")->process($pendingAction, $this, $output);
                    break;
                }

                default :
                {
                    $result = PendingAction::STATE_ERROR;
                    break;
                }
            }

            $this->getContainer()->get("cn_pending_actions.pending_actions_service")->setState($pendingAction, $result);
        }
    }
}
