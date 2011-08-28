<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Repository;

use Doctrine\ORM\EntityRepository;

class MemberRepository extends EntityRepository
{
    public function findBySearchQuery($queries, $limit, $offset)
    {
        $builder = $this->getSearchQuery($queries);
        if (!$builder) {
            return array();
        }

        if (null !== $limit) {
            $builder->setFirstResult($offset)->setMaxResults($limit);
        }

        $q = $builder->getQuery();

        $results = $q->getResult();

        return $results;
    }

    public function getSearchQuery($queries)
    {
        $results = array();

        $builder = $this->_em->createQueryBuilder();
        $builder->select('p')
            ->from('Societo\BaseBundle\Entity\MemberProfile', 'p')
            ->andWhere('p.profile = :profile')
        ;

        $ids = array();
        $isFirst = true;
        foreach ($queries as $k => $v) {
            $profile = $this->_em->getRepository('SocietoBaseBundle:Profile')->findOneBy(array(
                'name' => $k,
            ));

            if (!$profile) {
                continue;
            }

            $_builder = clone $builder;
            if ($profile->isSupportedPartialMatch()) {
               $_builder->andWhere('p.value LIKE :value');
               $v = '%'.$v.'%';
            } else {
                $_builder->andWhere('p.value = :value');
            }

            $_builder->setParameters(array('profile' => $profile->getId(), 'value' => $v));

            $q = $_builder->getQuery();
            $memberProfiles = $q->getResult();

            $_ids = array();
            foreach ($memberProfiles as $memberProfile) {
                $id = $memberProfile->getMember()->getId();
                $_ids[$id] = $id;
            }

            if ($isFirst) {
                $ids = $_ids;
            } else {
                foreach ($ids as $id) {
                    if (!isset($_ids[$id])) {
                        unset($ids[$id]);
                    }
                }
            }

            $isFirst = false;
        }

        $builder = $this->_em->createQueryBuilder();
        $builder->add('select', 'u')
            ->add('from', 'Societo\BaseBundle\Entity\Member u')
            ->add('orderBy', 'u.createdAt DESC');

        if (!$ids) {
            $builder->where('1 = 0');

            return $builder;
        }

        $ex = $this->_em->getExpressionBuilder();
        $builder->andWhere($ex->in('u.id', $ids));

        return $builder;
    }

    public function getAllMembers($limit, $offset)
    {
        $builder = $this->getAllMembersQuery();

        if (null !== $limit) {
            $builder->setFirstResult($offset)->setMaxResults($limit);
        }

        $q = $builder->getQuery();

        $results = $q->getResult();

        return $results;
    }

    public function getAllMembersQuery()
    {
        $builder = $this->_em->createQueryBuilder();
        $builder->select('u')
            ->from('Societo\BaseBundle\Entity\Member', 'u')
            ->orderBy('u.createdAt', 'DESC');
        ;

        return $builder;
    }
}
