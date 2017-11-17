<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Classes\Handlers;

use ClaviculaNox\PendingActionsBundle\Classes\Interfaces\HandlerInterface;
use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class EventHandler
 * @package ClaviculaNox\PendingActionsBundle\Classes\Handlers
 */
class EventHandler implements HandlerInterface
{
    /* @var EntityManager */
    protected $EntityManager;

    /* @var EventDispatcherInterface */
    protected $EventDispatcher;

    /**
     * EventHandlerService constructor.
     * @param EntityManager $EntityManager
     * @param EventDispatcherInterface $EventDispatcher
     */
    public function __construct(EntityManager $EntityManager, EventDispatcherInterface $EventDispatcher)
    {
        $this->EntityManager = $EntityManager;
        $this->EventDispatcher = $EventDispatcher;
    }

    /**
     * @param PendingAction $PendingAction
     * @return bool
     */
    public function checkPendingAction(PendingAction $PendingAction): bool
    {
        $params = json_decode($PendingAction->getActionParams(), true);

        return
            !is_null($params) &&
            isset($params["eventClassName"]) &&
            class_exists($params["eventClassName"]) &&
            isset($params["eventId"]) &&
            isset($params["args"]) &&
            $this->EventDispatcher->hasListeners($params["eventId"]);
    }

    /**
     * @param PendingAction $PendingAction
     * @return int
     */
    public function process(PendingAction $PendingAction): int
    {
        $params = json_decode($PendingAction->getActionParams(), true);

        $event = new \ReflectionClass($params["eventClassName"]);
        $event = $event->newInstanceArgs($params['args']);

        $this->EventDispatcher->dispatch($params['eventId'], $event);

        return PendingAction::STATE_PROCESSED;
    }
}
