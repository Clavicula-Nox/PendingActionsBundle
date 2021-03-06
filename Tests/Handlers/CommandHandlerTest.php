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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class CommandHandlerTest.
 */
class CommandHandlerTest extends WebTestCase
{
    /* @var array */
    public static $params = ['command' => 'fake:command',
        'arguments' => [
            'argA' => 'argValA',
            'argB' => 'argValB',
        ],
        'options' => [
            'optionA' => 'optionValA',
            'optionB' => 'optionValB',
            'optionC' => 'optionValC',
        ],
    ];

    /* @var string */
    public static $handlerDefault = 'CommandHandler';

    /* @var string */
    public static $handlerConfig = 'CommandHandlerConfig';

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
}
