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

/**
 * @ORM\Entity(repositoryClass="Societo\BaseBundle\Repository\SignUpRepository")
 * @ORM\Table(name="sign_up")
 */
class SignUp extends BaseEntity
{
    /**
     * @ORM\Column(name="namespace", type="string")
     */
    private $namespace;

    /**
     * @ORM\Column(name="username", type="string")
     */
    private $username;

    /**
     * @ORM\Column(name="parameters", type="array")
     */
    private $parameters;

    public function __construct($namespace, $username, $parameters = array())
    {
        $this->setNamespace($namespace);
        $this->setUsername($username);
        $this->setParameters($parameters);
    }

    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getUsername()
    {
        return $this->username;
    }

    // TODO: need to refactor or remove this
    public function isAdmin()
    {
        return false;
    }

    // TODO: need to refactor or remove this
    public function getLastLogin()
    {
        return false;
    }
}
