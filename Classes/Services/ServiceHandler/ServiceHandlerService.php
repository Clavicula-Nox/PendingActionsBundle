<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Classes\Services\ServiceHandler;

use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class ServiceHandlerService
 * @package ClaviculaNox\PendingActionsBundle\Classes\Services\ServiceHandler
 */
class ServiceHandlerService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

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
     * @param int $type
     * @param array $params
     * @param null|string $group
     * @return PendingAction
     */
    public function register($params = array(), $group = null)
    {
        $PendingAction = new PendingAction();
        $PendingAction->setType(PendingAction::TYPE_SERVICE);
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
     * @return bool
     */
    private function checkPendingAction(PendingAction $PendingAction)
    {
        $params = json_decode($PendingAction->getActionParams(), true);
        if (is_null($params)) {
            return false;
        }

        if (!isset($params["serviceId"])) {
            return false;
        }

        if (!isset($params["method"])) {
            return false;
        }

        if (!isset($params["args"])) {
            return false;
        }

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
     * @return int
     */
    public function process(PendingAction $PendingAction)
    {
        if (!$this->checkPendingAction($PendingAction)) {
            return PendingAction::STATE_ERROR;
        }

        $params = json_decode($PendingAction->getActionParams(), true);
        call_user_func_array(array($this->container->get($params["serviceId"]), $params["method"]), $params['args']);

        return PendingAction::STATE_PROCESSED;
    }
}
