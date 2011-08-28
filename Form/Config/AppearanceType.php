<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Form\Config;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityManager;

class AppearanceType extends AbstractType
{
    private $plugins = array();

    public function __construct($plugins)
    {
        $this->plugins = array_combine($plugins, $plugins);
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('skin', 'choice', array(
            'choices' => $this->plugins,
        ));
    }

    public function getName()
    {
        return 'societo_base_config_appearance';
    }
}
