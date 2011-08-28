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

class SignUpRepository extends EntityRepository
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
        $query = '';
        if (isset($queries['nickname']))
        {
            $query = $queries['nickname'];
        }

        if (!$query) {
            return null;
        }

        $builder = $this->_em->createQueryBuilder();
        $builder->select('s')
            ->from('Societo\BaseBundle\Entity\SignUp', 's')
            ->where('s.username LIKE :keyword')
            ->setParameter('keyword', '%'.$query.'%')
            ->orderBy('s.createdAt', 'DESC');
        ;

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
        $builder->select('s')
            ->from('Societo\BaseBundle\Entity\SignUp', 's')
            ->orderBy('s.createdAt', 'DESC');
        ;

        return $builder;
    }
}
