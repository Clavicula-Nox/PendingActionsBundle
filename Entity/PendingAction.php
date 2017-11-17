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
    const STATE_HANDLER_ERROR = 5;

    public static $labels = [
        self::STATE_WAITING => 'Waiting',
        self::STATE_PROCESSING => 'Processing',
        self::STATE_PROCESSED => 'Processed',
        self::STATE_ERROR => 'Error',
        self::STATE_UNKNOWN_HANDLER => 'Unknown Handler',
        self::STATE_HANDLER_ERROR => 'Handler Error',
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
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getHandler(): string
    {
        return $this->handler;
    }

    /**
     * @param string $handler
     */
    public function setHandler(string $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @return string
     */
    public function getActionParams(): string
    {
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
    public function getActionGroup(): string
    {
        return $this->actionGroup;
    }

    /**
     * @param string $actionGroup
     */
    public function setActionGroup(string $actionGroup)
    {
        $this->actionGroup = $actionGroup;
    }

    /**
     * @return \DateTime
     */
    public function getCreated(): \DateTime
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated(\DateTime $created)
    {
        $this->created = $created;
    }

    /**
     * @return \DateTime
     */
    public function getUpdated(): \DateTime
    {
        return $this->updated;
    }

    /**
     * @param \DateTime $updated
     */
    public function setUpdated(\DateTime $updated)
    {
        $this->updated = $updated;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @return string
     */
    public function getStateLabel(): string
    {
        return array_key_exists($this->state, self::$labels) ? self::$labels[$this->state] : 'Unknown Label';
    }

    /**
     * @param int $state
     */
    public function setState(int $state)
    {
        $this->state = $state;
    }
}
