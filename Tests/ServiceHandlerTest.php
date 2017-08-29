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
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class ServiceHandlerTest
 * @package ClaviculaNox\PendingActionsBundle\Tests
 */
class ServiceHandlerTest extends WebTestCase
{
    private $params = [
        "serviceId" => "fake.service",
        "method" => "fakeMethod",
        "args" => array(
            "mode" => "defaultMode",
            "title" => "defaultTitle"
        )
    ];

    private $group = "testGroup";

    /**
     * @return KernelInterface
     */
    protected function getKernel($options = [])
    {
        return $this->bootKernel($options);
    }

    /**
     * @return PendingAction
     */
    private function getPendingAction()
    {
        return $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions.service_handler")->register(
            $this->params,
            $this->group
        );
        /*$entityManagerMock = $this->createMock(EntityManager::class);
        $serviceHandler = new ServiceHandlerService($entityManagerMock);

        return $serviceHandler->register(
            $this->params,
            $this->group
        );*/
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

        $this->assertEquals($this->group, $Action->getActionGroup());
    }

    public function testPendingAction()
    {
        $Action = $this->getPendingAction();
        echo $this->getKernel()->getContainer()->get("cn_pending_actions.pending_actions.service_handler")->process($Action);die();
    }
}
