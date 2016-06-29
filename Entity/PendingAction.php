<?php

namespace ClaviculaNox\CNPendingActionsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package CNPendingActionsBundle\Entity
 *
 * @ORM\Entity(repositoryClass="ClaviculaNox\CNPendingActionsBundle\Entity\Repository\PendingActionRepository")
 * @ORM\Table(name="pending_actions")
 */
class PendingAction
{
    const STATE_WAITING = 0;
    const STATE_PROCESSING = 1;
    const STATE_PROCESSED = 2;
    const STATE_ERROR = 3;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=500, options={"default":""})
     */
    protected $action = '';

    /**
     * @ORM\Column(type="string", length=500, options={"default":""})
     */
    protected $actionParams = '';

    /**
     * @ORM\Column(type="string", length=500, options={"default":""})
     */
    protected $actionGroup = '';

    /**
     * @ORM\Column(type="integer")
     */
    protected $state;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getActionParams($asArray = false)
    {
        if ($asArray) {
            return json_decode($this->actionParams, true);
        }
        return $this->actionParams;
    }

    /**
     * @param array|string $actionParams
     */
    public function setActionParams($actionParams)
    {
        if (is_array($actionParams)) {
            $actionParams = json_encode($actionParams);
        }

        if (!json_decode($actionParams)) {
            $actionParams = '';
        }

        $this->actionParams = $actionParams;
    }

    /**
     * @return string
     */
    public function getActionGroup()
    {
        return $this->actionGroup;
    }

    /**
     * @param string $actionGroup
     */
    public function setActionGroup($actionGroup)
    {
        $this->actionGroup = $actionGroup;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }
}
