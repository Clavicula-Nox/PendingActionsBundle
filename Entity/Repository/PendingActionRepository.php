<?php

namespace ClaviculaNox\CNPendingActionsBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class PendingActionRepository
 * @package ClaviculaNox\CNPendingActionsBundle\Entity\Repository
 */
class PendingActionRepository extends EntityRepository
{
    /**
     * @param string $group
     * @return array
     */
    public function get($group, $state = null)
    {
        $builder = $this
            ->createQueryBuilder('pa')
            ->where('pa.state = :state');

        if (!is_null($group)) {
            $builder->andWhere('pa.group = :group');
            $builder->setParameter('group', $group);
        }

        $builder->setParameter('state', $state);

        return $builder->getQuery()->getResult();
    }
}
