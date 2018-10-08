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
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class ServiceHandlerService.
 */
class ServiceHandler implements ContainerAwareInterface, HandlerInterface
{
    use ContainerAwareTrait;

    /* @var EntityManager */
    protected $EntityManager;

    /**
     * ServiceHandlerService constructor.
     *
     * @param EntityManager $EntityManager
     */
    public function __construct(EntityManager $EntityManager)
    {
        $this->EntityManager = $EntityManager;
    }

    /**
     * @param PendingAction $PendingAction
     *
     * @return bool
     */
    public function checkPendingAction(PendingAction $PendingAction): bool
    {
        $params = json_decode($PendingAction->getActionParams(), true);
        if (
            is_null($params) ||
            !isset($params['serviceId']) ||
            !isset($params['method']) ||
            !isset($params['args']) ||
            !$this->container->has($params['serviceId'])
        ) {
            return false;
        }

        $service = $this->container->get($params['serviceId']);

        return method_exists($service, $params['method']);
    }

    /**
     * @param PendingAction $PendingAction
     *
     * @return int
     */
    public function process(PendingAction $PendingAction): int
    {
        $params = json_decode($PendingAction->getActionParams(), true);
        call_user_func_array([$this->container->get($params['serviceId']), $params['method']], $params['args']);

        return PendingAction::STATE_PROCESSED;
    }
}
