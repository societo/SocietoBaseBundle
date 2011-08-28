<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle;

use Societo\ActivityBundle\ActivityGenerator\DoctrineEntityGenerator;
use Societo\ActivityBundle\ActivityObject;

class ActivityGenerator extends DoctrineEntityGenerator
{
    const ACTIVITY_TYPE_MEMBER_REGISTER = 'register_member';

    public function getAvailableType()
    {
        return array(
            self::ACTIVITY_TYPE_MEMBER_REGISTER => 'Member joins this site',
        );
    }

    protected function getPersistActivity($entity, $em)
    {
        $this->registerActivity($em, $entity, 'join', new ActivityObject(), new ActivityObject('this site'), '', self::ACTIVITY_TYPE_MEMBER_REGISTER);
    }

    protected function getUpdateActivity($entity, $em)
    {
        // do nothing
    }
}
