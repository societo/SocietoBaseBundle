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
 * @ORM\Entity
 * @ORM\Table(name="menu")
 */
class Menu extends BaseEntity
{
    /**
     * @ORM\Column(name="name", type="string")
     */
    private $name;

    /**
     * @ORM\Column(name="parameters", type="array")
     */
    private $parameters = array();

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function hasItem($caption)
    {
        return isset($this->parameters[$caption]);
    }

    public function getItem($caption)
    {
        if (!$this->hasItem($caption)) {
            throw new \OutOfBoundsException();
        }

        return $this->parameters[$caption];
    }

    public function removeItem($caption)
    {
        unset($this->parameters[$caption]);
    }

    public function addUri($caption, $uri)
    {
        $this->parameters[$caption] = array('uri' => $uri);
    }

    public function addRoute($caption, $route, $parameters = array())
    {
        $this->parameters[$caption] = array(
            'route'      => $route,
            'parameters' => $parameters,
        );
    }
}
