<?php

/*
* This file is part of the PendingActionsBundle.
*
* (c) Adrien Lochon <adrien@claviculanox.io>
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace ClaviculaNox\PendingActionsBundle\Tests;

use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use ClaviculaNox\PendingActionsBundle\Tests\FakeBundle\Classes\FakeService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ServiceHandlerTest
 * @package ClaviculaNox\PendingActionsBundle\Tests
 */
class ServiceHandlerTest extends WebTestCase
{
    public static $params = [
        "serviceId" => "fake.service",
        "method" => "fakeMethod",
        "args" => array(
            "mode" => FakeService::MODE,
            "title" => FakeService::TITLE
        )
    ];

    public static $group = "testGroup";

    /**
     * @return KernelInterface
     */
    private function getKernel($options = [])
    {
        return $this->bootKernel($options);
    }

    /**
     * @return PendingAction
     */
    private function getPendingAction()
    {
        return $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions.service_handler")->register(
            ServiceHandlerTest::$params,
            ServiceHandlerTest::$group
        );
    }

    public function testRegistration()
    {
        $Action = $this->getPendingAction();

        if ($Action instanceof PendingAction) {
            $result = true;
        } else {
            $result = false;
        }

        $this->assertTrue($result);
    }

    public function testGroup()
    {
        $Action = $this->getPendingAction();

        $this->assertEquals(ServiceHandlerTest::$group, $Action->getActionGroup());
    }

    public function testPendingAction()
    {
        $this->getKernel()->getContainer()->set('fake.service', new FakeService());
        $Action = $this->getPendingAction();
        $result = $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions.service_handler")->process($Action);
        $this->assertEquals($result, PendingAction::STATE_PROCESSED);
    }
}
