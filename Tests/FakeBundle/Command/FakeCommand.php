<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Command;

use ClaviculaNox\PendingActionsBundle\Tests\CommandHandlerTest;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
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
            ->addArgument('argA', InputArgument::REQUIRED, 'The action group')
            ->addArgument('argB', InputArgument::OPTIONAL, 'The action group')
            ->addOption('optionA', null, InputOption::VALUE_REQUIRED, '', null)
            ->addOption('optionB', null, InputOption::VALUE_REQUIRED, '', null)
            ->addOption('optionC', null, InputOption::VALUE_REQUIRED, '', null)
            ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (
            $input->getOption("optionA") != CommandHandlerTest::$params["options"]["optionA"] ||
            $input->getOption("optionB") != CommandHandlerTest::$params["options"]["optionB"] ||
            $input->getOption("optionC") != CommandHandlerTest::$params["options"]["optionC"]
        ) {
            throw new \Exception();
        }
        if (
            $input->getArgument("argA") != CommandHandlerTest::$params["arguments"]["argA"] ||
            $input->getArgument("argB") != CommandHandlerTest::$params["arguments"]["argB"]
        ) {
            throw new \Exception();
        }
    }
}
