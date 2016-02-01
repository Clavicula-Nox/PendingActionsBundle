<?php

namespace ClaviculaNox\CNPendingActionsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @package CNPendingActionsBundle\Entity
 *
 * @ORM\Entity(repositoryClass="ClaviculaNox\CNPendingActionsBundle\Entity\Repository\PendingActionRepository")
 * @ORM\Table(name="pending_actions")
 *
 * @TODO : Maybe add an execution date for process ?
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
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return mixed
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param mixed $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }


}