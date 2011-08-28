<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Societo\BaseBundle\Repository\MemberRepository")
 * @ORM\Table(name="member")
 */
class Member extends BaseEntity
{
    /**
     * @ORM\Column(name="display_name", type="string")
     */
    protected $displayName;

    /**
     * @ORM\Column(name="is_admin", type="boolean")
     */
    protected $isAdmin = false;

    /**
     * @ORM\Column(name="is_locked", type="boolean")
     */
    protected $locked = false;

    /**
     * @ORM\OneToMany(targetEntity="MemberProfile", mappedBy="member")
     */
    protected $profiles;

    /**
     * @ORM\Column(name="last_login", type="datetime", nullable=true)
     */
    protected $lastLogin;

    // TODO
    protected $profileValues;

    // TODO
    public function getProfile()
    {
        if ($this->profileValues) {
            return $this->profileValues;
        }

        $this->profileValues = new \Societo\BaseBundle\Util\MemberProfileBag($this->profiles);

        return $this->profileValues;
    }

    public function getRawProfile($name, $isObject = false)
    {
        $profiles = $this->profiles;
        foreach ($profiles as $profile) {
            if ($profile->getProfile()->getName() === $name) {
                return ($isObject) ? $profile : $profile->getValue();
            }
        }

        return false;
    }

    public function getUsername()
    {
        $username = $this->getDisplayName();
        if ($username) {
            return $username;
        }

        // TODO
        return $this->getProfile()->nickname;
    }

    public function getDescription()
    {
        // TODO
        return $this->getProfile()->self_introduction;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;
    }

    public function getIsAdmin()
    {
        return $this->isAdmin();
    }

    public function setIsAdmin($bool)
    {
        $this->isAdmin($bool);
    }

    public function getLocked()
    {
        return $this->locked;
    }

    public function setLocked($bool)
    {
        $this->locked = $bool;
    }

    public function isAdmin($bool = null)
    {
        if (null !== $bool) {
            $this->isAdmin = (bool)$bool;
        }

        return $this->isAdmin;
    }

    public function getLastLogin()
    {
        return $this->lastLogin;
    }

    public function modifyLastLogin()
    {
        $this->lastLogin = new \DateTime();
    }

    public function modifyUpdatedAt()
    {
    }

    public function __toString()
    {
        return $this->getUsername();
    }
}
