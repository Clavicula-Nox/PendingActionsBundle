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
use ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeEvent;
use ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeException;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class EventHandlerTest.
 */
class EventHandlerTest extends WebTestCase
{
    /* @var array */
    public static $params = [
        'eventClassName' => "\ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeEvent",
        'eventId' => 'fake_event.fake_method',
        'args' => [
            'argA' => FakeEvent::ARG_A,
            'argB' => FakeEvent::ARG_B,
        ],
    ];

    /* @var array */
    public static $paramsException = [
        'eventClassName' => "\ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeEvent",
        'eventId' => 'fake_event.fake_method_exception',
        'args' => [
            'argA' => FakeEvent::ARG_A,
            'argB' => FakeEvent::ARG_B,
        ],
    ];

    /* @var string */
    public static $handlerDefault = 'EventHandler';

    /* @var string */
    public static $handlerConfig = 'EventHandlerConfig';

    /* @var string */
    public static $group = 'testGroup';

    /**
     * @return KernelInterface
     */
    private function getKernel($options = []): KernelInterface
    {
        return $this->bootKernel($options);
    }

    /**
     * @return PendingAction
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function getExceptionPendingAction(): PendingAction
    {
        return $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->register(
            self::$handlerDefault,
            self::$paramsException,
            self::$group
        );
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
        $Action = $this->getExceptionPendingAction();

        try {
            $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->process($Action);
            $this->fail('Expected Exception has not been raised.');
        } catch (FakeException $e) {
            $this->assertEquals($e->getMessage(), FakeException::FAKE_MESSAGE);
        }

        $Action = $this->getPendingAction(self::$handlerDefault);

        $result = $this->getKernel()->getContainer()->get('cn_pending_actions.pending_actions_service')->process($Action);
        $this->assertEquals($result, PendingAction::STATE_PROCESSED);
    }
}
