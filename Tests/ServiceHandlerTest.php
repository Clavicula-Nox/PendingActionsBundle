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

use ClaviculaNox\PendingActionsBundle\Classes\Services\ServiceHandler\ServiceHandlerService;
use ClaviculaNox\PendingActionsBundle\Entity\PendingAction;
use Doctrine\ORM\EntityManager;
use PHPUnit\Framework\TestCase;

/**
 * Class ServiceHandlerTest
 * @package ClaviculaNox\PendingActionsBundle\Tests
 */
class ServiceHandlerTest extends TestCase
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
     * @return PendingAction
     */
    private function getPendingAction()
    {
        $entityManagerMock = $this->createMock(EntityManager::class);
        $serviceHandler = new ServiceHandlerService($entityManagerMock);

        return $serviceHandler->register(
            $this->params,
            $this->group
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

        $this->assertEquals($this->group, $Action->getActionGroup());
    }

    public function testPendingAction()
    {
        $Action = $this->getPendingAction();

        $this->assertEquals($this->group, $Action->getActionGroup());
    }
}
