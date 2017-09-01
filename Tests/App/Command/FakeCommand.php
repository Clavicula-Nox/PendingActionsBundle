<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Tests\App\Command;

use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class FakeCommand
 * @package ClaviculaNox\PendingActionsBundle\Tests\App\Command
 */
class FakeCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('fake:command')
            ->setDescription('Fake Command for Unit Testing')
            ->setDefinition(
                array(
                    new InputArgument('actionGroup', InputArgument::OPTIONAL, 'The action group')
                )
            );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
echo "lololol";
    }
}
