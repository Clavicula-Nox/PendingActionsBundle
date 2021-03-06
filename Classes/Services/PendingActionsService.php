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

use ClaviculaNox\PendingActionsBundle\Classes\Exceptions\HandlerErrorException;
use ClaviculaNox\PendingActionsBundle\Classes\Interfaces\HandlerInterface;
use ClaviculaNox\PendingActionsBundle\Classes\Interfaces\HandlerRegisterInterface;
use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Doctrine\ORM\EntityManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Class PendingActionsService.
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
     *
     * @param EntityManager $EntityManager
     * @param array         $handlersList
     */
    public function __construct(EntityManager $EntityManager, array $handlersList)
    {
        $this->EntityManager = $EntityManager;
        $this->handlersList = $handlersList;
    }

    /**
     * @param string|null $group
     *
     * @return array
     */
    public function getPendingActions(string $group = null): array
    {
        return $this->EntityManager->getRepository('PendingActionsBundle:PendingAction')->get($group, PendingAction::STATE_WAITING);
    }

    /**
     * @param string      $handler
     * @param array       $params
     * @param string|null $group
     *
     * @return PendingAction
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Exception
     */
    public function register(string $handler, array $params = [], string $group = null): PendingAction
    {
        try {
            if (!array_key_exists($handler, $this->handlersList)) {
                throw new ServiceNotFoundException($handler);
            }

            $handlerService = $this->container->get($this->handlersList[$handler]);

            if ($handlerService instanceof HandlerRegisterInterface) {
                return $handlerService->register($params, $group);
            } else {
                $PendingAction = new PendingAction();
                $PendingAction->setHandler($handler);
                $PendingAction->setActionParams(json_encode($params));
                $PendingAction->setActionGroup($group);
                $PendingAction->setCreated(new \DateTime());
                $PendingAction->setUpdated(new \DateTime());
                $PendingAction->setState(PendingAction::STATE_WAITING);
                $this->EntityManager->persist($PendingAction);
                $this->EntityManager->flush();

                return $PendingAction;
            }
        } catch (ServiceNotFoundException $ServiceNotFoundException) {
            throw new \Exception(sprintf('The handler "%s" is not registered as a service.', $handler));
        }
    }

    /**
     * @param PendingAction $PendingAction
     * @param int           $stateId
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
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
     *
     * @return int
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function process(PendingAction $PendingAction): int
    {
        try {
            if (!array_key_exists($PendingAction->getHandler(), $this->handlersList)) {
                $this->setState($PendingAction, PendingAction::STATE_ERROR);
            } elseif (!$this->container->has($this->handlersList[$PendingAction->getHandler()])) {
                $this->setState($PendingAction, PendingAction::STATE_UNKNOWN_HANDLER);
            } else {
                $handler = $this->container->get($this->handlersList[$PendingAction->getHandler()]);
                if (!$handler instanceof HandlerInterface) {
                    $this->setState($PendingAction, PendingAction::STATE_HANDLER_ERROR);
                } elseif (!$this->container->get($this->handlersList[$PendingAction->getHandler()])->checkPendingAction($PendingAction)) {
                    $this->setState($PendingAction, PendingAction::STATE_ERROR);
                } else {
                    $return = $this->container->get($this->handlersList[$PendingAction->getHandler()])->process($PendingAction);
                    $this->setState($PendingAction, $return);
                }
            }
        } catch (HandlerErrorException $HandlerErrorException) {
            $this->setState($PendingAction, PendingAction::STATE_ERROR);
        }

        return $PendingAction->getState();
    }
}
