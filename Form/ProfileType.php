<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityManager;

class ProfileType extends AbstractType
{
    private $em;

    public static $supportableFields = array(
        'text' => array(),
        'long text' => array(
            'field' => 'textarea',
        ),
        'integer' => array(
            'field' => 'text',
            'validators' => array('Regex' => array('pattern' => '/^[0-9]+$/')),
        ),
        'list' => array(
            'field' => 'choice',
        ),
        'date' => array(),
        'birthday' => array(),
        'boolean' => array(
            'field' => 'checkbox',
        ),
    );

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $keys = array_keys(self::$supportableFields);

        $builder
            ->add('name')
            ->add('isRequired', null, array('required' => false))
            ->add('fieldType', 'choice', array('choices' => array_combine($keys, $keys)))
            ->add('list', 'textarea', array('required' => false))
            ->add('label', 'text', array('required' => false))
            ->add('help', 'text', array('required' => false));
    }

    public function getDefaultOptions(array $options)
    {
        $options = parent::getDefaultOptions($options);

        $options['data_class'] = 'Societo\BaseBundle\Entity\Profile';

        return $options;
    }

    public function getName()
    {
        return 'societo_base_profile';
    }
}
