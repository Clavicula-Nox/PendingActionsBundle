<?php

namespace ClaviculaNox\CNPendingActionsBundle\Classes\Services;

use ClaviculaNox\CNPendingActionsBundle\Entity\PendingAction;
use Doctrine\ORM\EntityManager;

/**
 * Class PendingActionsService
 * @package ClaviculaNox\CNPendingActionsBundle\Classes\Services
 */
class PendingActionsService
{
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
        $actions = $this->EntityManager->getRepository('CNPendingActionsBundle:PendingAction')->get($group, PendingAction::STATE_WAITING);

        if ($groupSimilarAction) {
            $returnActions = array();

            foreach ($actions as $action)
            {
                /* @var PendingAction $action */
                $key = sha1($action->getAction() . $action->getActionGroup() . json_encode($action->getAction()));
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
     * @param string $action
     * @param array $params
     * @param null|string $group
     */
    public function register($action, $params = array(), $group = null)
    {
        $PendingAction = new PendingAction();
        $PendingAction->setAction($action);
        $PendingAction->setActionParams($params);
        $PendingAction->setActionGroup($group);
        $PendingAction->setState(PendingAction::STATE_WAITING);
        $this->EntityManager->persist($PendingAction);
        $this->EntityManager->flush();
    }
}
