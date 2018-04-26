<?php

/*
* This file is part of the PendingActionsBundle.
*
* (c) Adrien Lochon <adrien@claviculanox.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * Class AppKernel.
 */
class AppKernel extends Kernel
{
    /**
     * @return array
     */
    public function registerBundles(): array
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new ClaviculaNox\PendingActionsBundle\PendingActionsBundle(),
            new ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\FakeBundle(),
        );

        return $bundles;
    }

    /**
     * @param LoaderInterface $loader
     *
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yaml');
    }

    /**
     * @return string
     */
    public function getCacheDir(): string
    {
        return __DIR__.'/../../build/cache/'.$this->getEnvironment();
    }

    /**
     * @return string
     */
    public function getLogDir(): string
    {
        return __DIR__.'/../../build/kernel_logs/'.$this->getEnvironment();
    }
}
