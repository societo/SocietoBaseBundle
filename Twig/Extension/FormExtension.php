<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Twig\Extension;

use Symfony\Bridge\Twig\Extension\FormExtension as BaseFormExtension;
use Symfony\Component\Form\FormView;

class FormExtension extends BaseFormExtension
{
    public function getFunctions()
    {
        $result = parent::getFunctions();
        $result['form_help'] = new \Twig_Function_Method($this, 'renderHelp', array('is_safe' => array('html')));

        return $result;
    }

    public function renderHelp(FormView $view, $help = null)
    {
        return $this->render($view, 'help', null === $help ? array() : array('help' => $help));
    }
}
