<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Classes\Services;

use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class PendingActionsService
 * @package ClaviculaNox\PendingActionsBundle\Classes\Services
 */
class PendingActionsService implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected $EntityManager;

    /**
     * PendingActionsService constructor.
     * @param EntityManager $EntityManager
     */
    public function __construct(EntityManager $EntityManager)
    {
        $this->EntityManager = $EntityManager;
    }

    /**
     * @param null|string $group
     * @return array
     */
    public function getPendingActions($group = null, $groupSimilarAction = false)
    {
        $actions = $this->EntityManager->getRepository('PendingActionsBundle:PendingAction')->get($group, PendingAction::STATE_WAITING);

        if ($groupSimilarAction) {
            $returnActions = array();

            foreach ($actions as $action)
            {
                /* @var PendingAction $action */
                $key = sha1($action->getType() . $action->getActionGroup() . $action->getActionParams());
                if (array_key_exists($key, $returnActions)) {
                    $this->EntityManager->remove($action);
                } else {
                    $returnActions[$key] = $action;
                }
            }
            $this->EntityManager->flush();
            $returnActions = array_values($returnActions);

            return $returnActions;
        }

        return $actions;
    }

    /**
     * @param $type
     * @param array $params
     * @param null|string $group
     * @return PendingAction
     */
    public function register($type, $params = array(), $group = null)
    {
        $PendingAction = new PendingAction();
        $PendingAction->setType($type);
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
        switch ($PendingAction->getType())
        {
            case PendingAction::TYPE_SERVICE :
            {
                $params = json_decode($PendingAction->getActionParams(), true);
                if (is_null($params)) {
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

            default :
            {
                return false;
            }
        }
    }

    /**
     * @param PendingAction $PendingAction
     * @param $stateId
     */
    public function setState(PendingAction $PendingAction, $stateId)
    {
        $PendingAction->setState($stateId);
        $this->EntityManager->persist($PendingAction);
        $this->EntityManager->flush();
    }
}
