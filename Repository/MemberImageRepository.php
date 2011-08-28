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

class MemberImageRepository extends EntityRepository
{
    public function setMemberImage($member, $file)
    {
        $image = $this->findOneBy(array(
            'member' => $member->getId(),
        ));
        if (!$image) {
            $image = $this->_class->newInstance();
            $image->setMember($member);
        }

        $image->setImageFile($file);
        $this->_em->persist($image);
        $this->_em->flush();
    }
}
