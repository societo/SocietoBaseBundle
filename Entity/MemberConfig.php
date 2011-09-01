<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Societo\BaseBundle\Entity\Member;

/**
 * MemberConfig
 *
 * @ORM\Entity
 * @ORM\Table(name="member_config",
 *      uniqueConstraints={@ORM\UniqueConstraint(name="member_namespace_name",columns={"member_id", "namespace", "name"})}
 * )
 * @author Kousuke Ebihara <ebihara@php.net>
 */
class MemberConfig extends BaseEntity
{
    /**
     * @ORM\ManyToOne(targetEntity="Societo\BaseBundle\Entity\Member")
     * @ORM\JoinColumn(name="member_id", referencedColumnName="id")
     */
    private $member;

    /**
     * @ORM\Column(name="namespace", type="string")
     */
    private $namespace;

    /**
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @ORM\Column(name="value", type="string")
     */
    private $value;

    public function __construct(Member $member, $namespace, $name, $value)
    {
        $this->member = $member;
        $this->namespace = $namespace;
        $this->name = $name;
        $this->value = $value;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getMember()
    {
        return $this->member;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function __toString()
    {
        return $this->getValue();
    }
}
