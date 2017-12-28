<?php

/*
* This file is part of the PendingActionsBundle.
*
* (c) Adrien Lochon <adrien@claviculanox.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace ClaviculaNox\PendingActionsBundle\Tests\Handlers;

use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ServiceHandlerTest.
 */
class ServiceHandlerTest extends WebTestCase
{
    /* @var array */
    public static $params = [
        'serviceId' => 'fake.service',
        'method' => 'fakeMethod',
        'args' => array(
            'argA' => FakeService::ARG_A,
            'argB' => FakeService::ARG_B,
        ),
    ];

    /* @var string */
    public static $handlerDefault = 'ServiceHandler';

    /* @var string */
    public static $handlerConfig = 'ServiceHandlerConfig';

    /* @var string */
    public static $group = 'testGroup';

    /**
     * @param array $options
     *
     * @return KernelInterface
     */
    private function getKernel($options = []): KernelInterface
    {
        return $this->bootKernel($options);
    }

    /**
     * @param string $handler
     *
     * @return PendingAction
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function getPendingAction(string $handler): PendingAction
    {
        return $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->register(
            $handler,
            self::$params,
            self::$group
        );
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testRegistrationDefault(): void
    {
        $Action = $this->getPendingAction(self::$handlerDefault);

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $Action);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testRegistrationConfig(): void
    {
        $Action = $this->getPendingAction(self::$handlerConfig);

        $this->assertInstanceOf('\ClaviculaNox\PendingActionsBundle\Entity\PendingAction', $Action);
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testHandlerDefault(): void
    {
        $Action = $this->getPendingAction(self::$handlerDefault);

        $this->assertEquals(self::$handlerDefault, $Action->getHandler());
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testHandlerConfig(): void
    {
        $Action = $this->getPendingAction(self::$handlerConfig);

        $this->assertEquals(self::$handlerConfig, $Action->getHandler());
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testGroup(): void
    {
        $Action = $this->getPendingAction(self::$handlerDefault);

        $this->assertEquals(self::$group, $Action->getActionGroup());
    }

    /**
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function testPendingAction(): void
    {
        $Action = $this->getPendingAction(self::$handlerDefault);
        $result = $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->process($Action);
        $this->assertEquals($result, PendingAction::STATE_PROCESSED);
    }
}
