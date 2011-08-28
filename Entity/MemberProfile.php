<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Entity;

use Societo\BaseBundle\Entity\BaseEntity;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="member_profile")
 */
class MemberProfile extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Member")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    protected $member;

    /**
     * @ORM\ManyToOne(targetEntity="profile")
     * @ORM\JoinColumn(name="profile_id", referencedColumnName="id")
     */
    protected $profile;

    /**
     * @ORM\Column(name="value", type="string")
     */
    protected $value = '';

    public function __construct($member, $profile, $value = '')
    {
        $this->setMember($member);
        $this->setProfile($profile);
        $this->setValue($value);
    }

    public function setMember($member)
    {
        $this->member = $member;
    }

    public function getMember()
    {
        return $this->member;
    }

    public function setProfile($profile)
    {
        $this->profile = $profile;
    }

    public function getProfile()
    {
        return $this->profile;
    }

    public function setValue($value)
    {
        if ($this->getProfile() && $this->getProfile()->isDateTimeValue() && $value instanceof \DateTime) {
            $value = $value->format('Y-m-d H:i:s');
        }

        $this->value = (string)$value;
    }

    public function getValue()
    {
        $result = $this->value;

        if ($this->getProfile()->isDateTimeValue()) {
            $result = new \DateTime($result);
        } elseif ('boolean' === $this->getProfile()->getFieldType()) {
            $result = (bool)$result;
        }

        return $result;
    }
}
