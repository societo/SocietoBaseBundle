<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle;

class SiteConfig implements \ArrayAccess
{
    private $em;

    private $data = array();

    private $add = array();

    private $modify = array();

    private $remove = array();

    private $default = array();

    public function __construct($em, $data = array())
    {
        $this->em = $em;

        try {
            $configs = $this->em->getRepository('SocietoBaseBundle:SiteConfig')->findAll();
            foreach ($configs as $config) {
                $this->data[$config->getName()] = $config->getValue();
                $this->em->detach($config);
            }
        } catch (\Exception $e) {
            // do nothing (for avoiding error in non-initialized database)

            // TODO: 
        }
    }

    public function setDefault($name, $value)
    {
        $this->default[$name] = $value;
    }

    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    public function offsetGet($offset)
    {
        $default = null;
        if (isset($this->default[$offset])) {
            $default = $this->default[$offset];
        }

        return $this->offsetExists($offset) ? $this->data[$offset] : $default;
    }

    public function offsetSet($offset, $value)
    {
        if ($this->offsetExists($offset)) {
            $this->modify[] = $offset;
        } else {
            $this->add[] = $offset;
        }

        $this->data[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        $this->remove[] = $offset;

        unset($this->data[$offset]);
    }

    public function __get($offset)
    {
        return $this->offsetGet($offset);
    }

    public function __set($offset, $value)
    {
        $this->offsetSet($offset, $value);
    }

    public function clear()
    {
        $this->add = $this->modify = $this->remove = array();
    }

    public function flush()
    {
        foreach ($this->add as $add) {
            $this->em->persist(new \Societo\BaseBundle\Entity\SiteConfig($add, $this->offsetGet($add)));
        }

        foreach ($this->modify as $modify) {
            $config = $this->em->getRepository('SocietoBaseBundle:SiteConfig')->findOneBy(array(
                'name' => $modify,
            ));
            if ($config) {
                $config->setValue($this->offsetGet($modify));
                $this->em->persist($config);
            }
        }

        foreach ($this->remove as $remove) {
            $config = $this->em->getRepository('SocietoBaseBundle:SiteConfig')->findOneBy(array(
                'name' => $remove,
            ));
            if ($config) {
                $this->em->remove($config);
            }
        }

        $this->em->flush();
        $this->clear();
    }
}
