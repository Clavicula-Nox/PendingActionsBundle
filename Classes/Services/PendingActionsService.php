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

    public function getPendingActions($group = null)
    {
        return $this->EntityManager->getRepository('CNPendingActionsBundle:PendingAction')->get($group, PendingAction::STATE_WAITING);
    }
}