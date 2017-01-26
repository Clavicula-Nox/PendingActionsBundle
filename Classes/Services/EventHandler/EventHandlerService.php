<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Classes\Services\EventHandler;

use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class EventHandlerService
 * @package ClaviculaNox\PendingActionsBundle\Classes\Services\EventHandler
 */
class EventHandlerService
{
    protected $EntityManager;
    protected $EventDispatcher;

    /**
     * EventHandlerService constructor.
     * @param EntityManager $EntityManager
     * @param EventDispatcherInterface $EventDispatcher
     */
    public function __construct(
        EntityManager $EntityManager,
        EventDispatcherInterface $EventDispatcher
    )
    {
        $this->EntityManager = $EntityManager;
        $this->EventDispatcher = $EventDispatcher;
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
        $PendingAction->setType(PendingAction::TYPE_EVENT);
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

        if (!isset($params["eventId"])) {
            return false;
        }

        if (!isset($params["args"])) {
            return false;
        }

        if (!$this->EventDispatcher->hasListeners($params["eventId"]))
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
        if (!isset($params['subject'])) {
            $params['subject'] = null;
        }

        $event = new GenericEvent($params['subject'], $params['args']);
        $this->EventDispatcher->dispatch($params['eventId'], $event);

        return PendingAction::STATE_PROCESSED;
    }
}
