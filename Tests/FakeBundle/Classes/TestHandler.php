<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes;

use ClaviculaNox\PendingActionsBundle\Classes\Exceptions\HandlerErrorException;
use ClaviculaNox\PendingActionsBundle\Classes\Interfaces\HandlerInterface;
use ClaviculaNox\PendingActionsBundle\Classes\Interfaces\HandlerRegisterInterface;
use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Doctrine\ORM\EntityManager;

/**
 * Class TestHandler.
 */
class TestHandler implements HandlerInterface, HandlerRegisterInterface
{
    /* @var EntityManager */
    protected $EntityManager;

    /**
     * TestHandler constructor.
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
        return true;
    }

    /**
     * @param PendingAction $PendingAction
     *
     * @return int
     *
     * @throws HandlerErrorException
     */
    public function process(PendingAction $PendingAction): int
    {
        throw new HandlerErrorException('Nothing to see here.');
    }

    /**
     * @param array       $params
     * @param string|null $group
     *
     * @return PendingAction
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function register(array $params = [], string $group = null): PendingAction
    {
        $PendingAction = new PendingAction();
        $PendingAction->setHandler('TestHandler');
        $PendingAction->setActionParams(json_encode($params));
        $PendingAction->setActionGroup($group);
        $PendingAction->setCreated(new \DateTime());
        $PendingAction->setUpdated(new \DateTime());
        $PendingAction->setState(PendingAction::STATE_WAITING);
        $this->EntityManager->persist($PendingAction);
        $this->EntityManager->flush();

        return $PendingAction;
    }
}
