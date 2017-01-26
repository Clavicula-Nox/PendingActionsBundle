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

use ClaviculaNox\PendingActionsBundle\Classes\Services\CommandHandler\CommandHandlerService;
use ClaviculaNox\PendingActionsBundle\Classes\Services\EventHandler\EventHandlerService;
use ClaviculaNox\PendingActionsBundle\Classes\Services\ServiceHandler\ServiceHandlerService;
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
    protected $ServiceHandlerService;
    protected $EventHandlerService;
    protected $CommandHandlerService;

    /**
     * PendingActionsService constructor.
     * @param EntityManager $EntityManager
     * @param ServiceHandlerService $ServiceHandlerService
     * @param EventHandlerService $EventHandlerService
     * @param CommandHandlerService $CommandHandlerService
     */
    public function __construct(
        EntityManager $EntityManager,
        ServiceHandlerService $ServiceHandlerService,
        EventHandlerService $EventHandlerService,
        CommandHandlerService $CommandHandlerService
    )
    {
        $this->EntityManager = $EntityManager;
        $this->ServiceHandlerService = $ServiceHandlerService;
        $this->EventHandlerService = $EventHandlerService;
        $this->CommandHandlerService = $CommandHandlerService;
    }

    /**
     * @param string|null $group
     * @param bool $groupSimilarAction
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
     * @param string|null $group
     * @return PendingAction|null
     */
    public function register($type, $params = array(), $group = null)
    {
        switch ($type)
        {
            case PendingAction::TYPE_SERVICE :
            {
                return $this->ServiceHandlerService->register($params, $group);
            }

            case PendingAction::TYPE_EVENT :
            {
                return $this->EventHandlerService->register($params, $group);
            }

            case PendingAction::TYPE_COMMAND :
            {
                return $this->CommandHandlerService->register($params, $group);
            }

            default :
            {
                return null;
            }
        }
    }

    /**
     * @deprecated Use Handler method instead
     * @return bool
     */
    public function checkPendingAction()
    {
        return true;
    }

    /**
     * @param PendingAction $PendingAction
     * @param integer $stateId
     */
    public function setState(PendingAction $PendingAction, $stateId)
    {
        $PendingAction->setState($stateId);
        $this->EntityManager->persist($PendingAction);
        $this->EntityManager->flush();
    }
}
