<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class PendingActionsExtension.
 */
class PendingActionsExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        //Add default handlers
        $config['handlers']['ServiceHandler'] = 'cn_pending_actions.pending_actions.service_handler';
        $config['handlers']['EventHandler'] = 'cn_pending_actions.pending_actions.event_handler';
        $config['handlers']['CommandHandler'] = 'cn_pending_actions.pending_actions.command_handler';

        foreach ($config as $key => $value) {
            $container->setParameter('pending_actions.'.$key, $value);
        }

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.yaml');
    }
}
