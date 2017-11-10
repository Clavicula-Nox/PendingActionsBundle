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

    /* @var EntityManager */
    protected $EntityManager;

    /* @var array */
    protected $handlersList;

    /**
     * PendingActionsService constructor.
     * @param EntityManager $EntityManager
     */
    public function __construct(EntityManager $EntityManager, array $handlersList)
    {
        $this->EntityManager = $EntityManager;
        $this->handlersList = $handlersList;
    }

    /**
     * @param string|null $group
     * @param bool $groupSimilarAction
     * @return array
     */
    public function getPendingActions($group = null, bool $groupSimilarAction = false): array
    {
        $actions = $this->EntityManager->getRepository('PendingActionsBundle:PendingAction')->get($group, PendingAction::STATE_WAITING);

        if ($groupSimilarAction) {
            $returnActions = [];

            foreach ($actions as $action)
            {
                /* @var PendingAction $action */
                $key = sha1($action->getHandler() . $action->getActionGroup() . $action->getActionParams());
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
     * @param string $handler
     * @param array $params
     * @param null|string $group
     * @return PendingAction
     */
    public function register(string $handler, array $params = [], $group = null): PendingAction
    {
        $PendingAction = new PendingAction();
        $PendingAction->setHandler($handler);
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
     * @param int $stateId
     */
    public function setState(PendingAction $PendingAction, int $stateId): void
    {
        $PendingAction->setState($stateId);
        $PendingAction->setUpdated(new \DateTime());
        $this->EntityManager->persist($PendingAction);
        $this->EntityManager->flush();
    }

    /**
     * @param PendingAction $PendingAction
     * @return int
     */
    public function process(PendingAction $PendingAction): int
    {
        if (!array_key_exists($PendingAction->getHandler(), $this->handlersList)) {
            $this->setState($PendingAction, PendingAction::STATE_ERROR);
        } elseif (!$this->container->has($this->handlersList[$PendingAction->getHandler()])) {
            $this->setState($PendingAction, PendingAction::STATE_UNKNOWN_HANDLER);
        } elseif (!$this->container->get($this->handlersList[$PendingAction->getHandler()])->checkPendingAction($PendingAction)) {
            $this->setState($PendingAction, PendingAction::STATE_ERROR);
        } else {
            $return = $this->container->get($this->handlersList[$PendingAction->getHandler()])->process($PendingAction);
            $this->setState($PendingAction, $return);
        }

        return $PendingAction->getState();
    }
}
