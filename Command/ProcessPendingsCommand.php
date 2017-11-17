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
 * Class ProcessPendingsCommand.
 */
class ProcessPendingsCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this
            ->setName('cn:pending-actions:process')
            ->setDescription('Processing of Pending Actions')
            ->addArgument('actionGroup', InputArgument::OPTIONAL, 'The action group')
            ->setHelp(<<<'EOT'
The <info>cn:pending-actions:process</info> command processes the pending actions of an action group : 

  <info>php %command.full_name% my_group_name</info>

The actionGroup parameter is optional, if not set, it'll process all pending actions.
EOT
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        $output->write('Getting pending actions...', true);
        if (!is_null($input->getArgument('actionGroup'))) {
            $output->write('   Selected group : '.$input->getArgument('actionGroup'), true);
        }

        $pendingActions = $this->getContainer()
            ->get('cn_pending_actions.pending_actions_service')
            ->getPendingActions($input->getArgument('actionGroup'), true);
        $total = count($pendingActions);

        if ($total > 0) {
            $output->write('Processing actions...', true);
        } else {
            $output->write('No actions to process...', true);
        }

        $counter = 1;

        foreach ($pendingActions as $pendingAction) {
            /* @var $pendingAction PendingAction */
            $pendingAction = $this->getContainer()->get('doctrine')->getRepository('PendingActionsBundle:PendingAction')->find($pendingAction->getId());
            $output->write('   - Action '.$counter.'/'.$total, true);
            ++$counter;

            if (PendingAction::STATE_WAITING != $pendingAction->getState()) {
                continue;
            }

            $this->getContainer()->get('cn_pending_actions.pending_actions_service')->setState($pendingAction, PendingAction::STATE_PROCESSING);

            //Special case for the default command handler, until i find a better way to do it
            if ('CommandHandler' == $pendingAction->getHandler()) {
                $result = $this->getContainer()->get('cn_pending_actions.pending_actions.command_handler')->process($pendingAction, $this, $output);
            } else {
                $result = $this->getContainer()->get('cn_pending_actions.pending_actions_service')->process($pendingAction);
            }

            $this->getContainer()->get('cn_pending_actions.pending_actions_service')->setState($pendingAction, $result);

            $output->write('   Action '.$pendingAction->getId().' : '.PendingAction::$labels[$result], true);
        }
    }
}
