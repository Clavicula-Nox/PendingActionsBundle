<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
    const STATE_UNKNOWN_HANDLER = 4;

    public static $labels = [
        PendingAction::STATE_WAITING => "Waiting",
        PendingAction::STATE_PROCESSING => "Processing",
        PendingAction::STATE_PROCESSED => "Processed",
        PendingAction::STATE_ERROR => "Error",
        PendingAction::STATE_UNKNOWN_HANDLER => "Unknown Handler"
    ];

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="text", options={"default":""})
     */
    protected $handler = '';

    /**
     * @var string
     * @ORM\Column(type="text", options={"default":""})
     */
    protected $actionParams = '';

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, length=500, options={"default":""})
     */
    protected $actionGroup = '';

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $created;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * @var int
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
    public function getHandler()
    {
        return $this->handler;
    }

    /**
     * @param string $handler
     */
    public function setHandler( $handler)
    {
        $this->handler = $handler;
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
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return int
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getStateLabel()
    {
        return array_key_exists($this->state, PendingAction::$labels) ? PendingAction::$labels[$this->state] : "Unknown Label";
    }

    /**
     * @param int $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }
}
