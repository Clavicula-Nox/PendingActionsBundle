<?php

/*
 * This file is part of the PendingActionsBundle.
 *
 * (c) Adrien Lochon <adrien@claviculanox.io>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ClaviculaNox\PendingActionsBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class PendingActionRepository.
 */
class PendingActionRepository extends EntityRepository
{
    /**
     * @param string   $group
     * @param int|null $state
     *
     * @return array
     */
    public function get(string $group, int $state = null): array
    {
        $builder = $this
            ->createQueryBuilder('pa')
            ->where('pa.state = :state')
            ->setParameter('state', $state);

        if (!is_null($group)) {
            $builder->andWhere('pa.actionGroup = :group');
            $builder->setParameter('group', $group);
        }

        return $builder->getQuery()->getResult();
    }
}
