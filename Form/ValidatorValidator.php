<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Form;

use Symfony\Component\Form\FormValidatorInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Validator\Constraint;

class ValidatorValidator implements FormValidatorInterface
{
    private $validator, $constraint, $parameters;

    public function __construct($validator, $constraint, $parameters = array())
    {
        $this->validator = $validator;
        $this->constraint = $constraint;
        $this->parameters = $parameters;
    }

    public function validate(FormInterface $form)
    {
        $constraint = $this->constraint;

        if (is_string($constraint)) {
            if (false === strpos($constraint, '\\')) {
                $constraint = '\Symfony\Component\Validator\Constraints\\'.$constraint;
            }

            $constraint = new $constraint($this->parameters);
        }

        if (!($constraint instanceof Constraint)) {
            throw new \Exception('Non validator constraint is specified');
        }

        $errors = $this->validator->validateValue($form->getData(), $constraint);
        foreach ($errors as $error) {
            $form->addError(new FormError($error->getMessage()));
        }
    }
}
