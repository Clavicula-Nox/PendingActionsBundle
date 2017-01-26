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

use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CommandHandlerService
 * @package ClaviculaNox\PendingActionsBundle\Classes\Services\CommandHandler
 */
class CommandHandlerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $EntityManager;
    protected $Kernel;

    /**
     * ServiceHandlerService constructor.
     * @param EntityManager $EntityManager
     */
    public function __construct(EntityManager $EntityManager, KernelInterface $Kernel)
    {
        $this->EntityManager = $EntityManager;
        $this->Kernel = $Kernel;
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
        $PendingAction->setState(PendingAction::STATE_WAITING);
        $this->EntityManager->persist($PendingAction);
        $this->EntityManager->flush();

        return $PendingAction;
    }

    /**
     * @param PendingAction $PendingAction
     * @return bool
     */
    public function checkPendingAction(PendingAction $PendingAction)
    {
        $params = json_decode($PendingAction->getActionParams(), true);
        if (is_null($params)) {
            return false;
        }

        $command = $this->Kernel->find('demo:greet');

        var_dump($command);
        die();

        if (!$this->container->has($params["serviceId"]))
        {
            return false;
        }

        $service = $this->container->get($params["serviceId"]);
        if (!method_exists($service, $params["method"]))
        {
            return false;
        }

        return true;
    }

    /**
     * @param PendingAction $PendingAction
     */
    public function process(PendingAction $PendingAction)
    {
        echo "laaaaaa";
        die();
        $params = json_decode($PendingAction->getActionParams(), true);
        call_user_func_array(array($this->container->get($params["serviceId"]), $params["method"]), $params['args']);
    }
}
