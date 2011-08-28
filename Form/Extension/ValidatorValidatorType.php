<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Societo\BaseBundle\Form\ValidatorValidator;

class ValidatorValidatorType extends AbstractTypeExtension
{
    private $validator;

    public function __construct($validator)
    {
        $this->validator = $validator;
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'validator' => array(),
        );
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        if (is_string($options['validator'])) {
            $validator = $options['validator'];
            $options['validator'] = array($validator => array());
        }

        if ($options['required']) {
            $options['validator']['NotNull'] = array();
        }

        if (empty($options['validator'])) {
            return null;
        }

        foreach ($options['validator'] as $validator => $parameter) {
            $builder->addValidator(new ValidatorValidator($this->validator, $validator, $parameter));
        }
    }

    public function getExtendedType()
    {
        return 'field';
    }
}
