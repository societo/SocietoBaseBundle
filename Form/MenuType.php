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
use Symfony\Component\Form\CallbackValidator;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManager;

class MenuType extends AbstractType
{
    private $pages = array();

    public function __construct($pages = array())
    {
        foreach ($pages as $page) {
            $this->pages[$page->getName()] = $page->getName();
        }
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('caption', 'text');
        $builder->add('uri', 'url', array('required' => false));
        $builder->add('route', 'choice', array(
            'required' => false,
            'choices' => $this->pages,
        ));
    }

    public function getName()
    {
        return 'societo_base_menu';
    }
}
