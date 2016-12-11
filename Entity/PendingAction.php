<?php

namespace ClaviculaNox\PendingActionsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package PendingActionsBundle\Entity
 *
 * @ORM\Entity(repositoryClass="ClaviculaNox\PendingActionsBundle\Entity\Repository\PendingActionRepository")
 * @ORM\Table(name="pending_actions")
 */
class PendingAction
{
    const STATE_WAITING = 0;
    const STATE_PROCESSING = 1;
    const STATE_PROCESSED = 2;
    const STATE_ERROR = 3;

    const TYPE_SERVICE = 1;
    const TYPE_EVENT = 2;

    /**
     * @var integer
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $type;

    /**
     * @var string
     * @ORM\Column(type="text", options={"default":""})
     */
    protected $actionParams = '';

    /**
     * @var string
     * @ORM\Column(type="string", length=500, options={"default":""})
     */
    protected $actionGroup = '';

    /**
     * @var integer
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
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return array|string
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
