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

class AuthenticationType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('self_registration', 'choice', array(
            'choices' => array(
                'disable'          => 'disabled',
                'admin_activation' => 'Activation by administrator',
                'free'             => 'Automatically activation',
            ),
        ));
    }

    public function getName()
    {
        return 'societo_base_config_authentication';
    }
}
