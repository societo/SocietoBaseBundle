<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Util;

// TODO: rename this
class MemberProfileBag extends \ArrayObject
{
    public function __construct($profiles)
    {
        $list = array();

        foreach ($profiles as $v) {
            try {
                $list[$v->getProfile()->getName()] = $v->getValue();
            } catch (\Doctrine\ORM\EntityNotFoundException $e) {
            }
        }

        parent::__construct($list);
    }

    public function __get($name)
    {
        if ($this->offsetExists($name)) {
            return $this->offsetGet($name);
        }

        return null;
    }

    public function __set($name, $value)
    {
        $this->offsetSet($name, $value);
    }
}
