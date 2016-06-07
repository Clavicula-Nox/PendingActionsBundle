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
    protected $params = '';

    /**
     * @ORM\Column(type="string", length=500, options={"default":""})
     */
    protected $group = '';

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
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $params
     */
    public function setParams($params)
    {
        if (is_array($params)) {
            $params = json_encode($params);
        }

        if (!json_decode($params)) {
            $params = '';
        }
        
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
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
