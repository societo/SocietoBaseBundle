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
use Societo\BaseBundle\Entity\BaseEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="profile")
 */
class Profile extends BaseEntity
{
    /**
     * @ORM\Column(type="string", length="32", unique=true)
     */
    protected $name;

    /**
     * @ORM\Column(name="is_required", type="boolean")
     */
    protected $isRequired = false;

    /**
     * @ORM\Column(name="field_type", type="string", length="32")
     */
    protected $fieldType = 'text';

    /**
     * @ORM\Column(name="field_parameters", name="field_parameters", type="array")
     */
    protected $fieldParameters = array();

    /**
     * @ORM\Column(name="label", type="text")
     */
    protected $label = '';

    /**
     * @ORM\Column(name="help", type="text")
     */
    protected $help = '';

    /**
     * Set display name of this person
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Get display name of this person
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get display name of this person
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($label)
    {
        $this->label = $label;
    }

    public function getIsRequired()
    {
        return $this->isRequired;
    }

    public function setIsRequired($isRequired)
    {
        $this->isRequired = $isRequired;
    }

    public function getFieldType()
    {
        return $this->fieldType;
    }

    public function setFieldType($fieldType)
    {
        $this->fieldType = $fieldType;
    }

    public function getFieldParameters()
    {
        return $this->fieldParameters;
    }

    public function setFieldParameter($name, $value)
    {
        $this->fieldParameters[$name] = $value;
    }

    public function hasFieldParameter($name)
    {
        return isset($this->fieldParameters[$name]);
    }

    public function getFieldParameter($name)
    {
        if (!$this->hasFieldParameter($name)) {
            // TODO: should throw an exception?
            return null;
        }

        return $this->fieldParameters[$name];
    }

    public function setList($list = '')
    {
        if ('list' === $this->getFieldType()) {
            $this->setFieldParameter('list', $list);
        }
    }

    public function getList($asArray = false)
    {
        if (!$this->hasFieldParameter('list') || 'list' !== $this->getFieldType()) {
            return null;
        }

        $list = trim($this->getFieldParameter('list'));
        if ($asArray) {
            $_list = explode("\n", $list);

            $list = array();
            foreach ($_list as $_item) {
                $_item = trim($_item);
                if ('' !== $_item) {
                    $list[] = $_item;
                }
            }
        }

        return $list;
    }

    public function getHelp()
    {
        return $this->help;
    }

    public function setHelp($help)
    {
        $this->help = (string)$help;
    }

    public function isDateTimeValue()
    {
        return ($this->getFieldType() === 'date' || $this->getFieldType() === 'birthday');
    }

    public function isSupportedPartialMatch()
    {
        $types = array(
            'text', 'long text', 'integer',
        );

        return in_array($this->getFieldType(), $types);
    }
}
