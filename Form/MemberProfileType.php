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
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

class MemberProfileType extends AbstractType
{
    private $em, $profiles, $validator;

    // TODO: remove
    private $embed = false;

    public function __construct(EntityManager $em, $profiles, $validator, $embed = false)
    {
        $this->em = $em;
        $this->profiles = $profiles;
        $this->validator = $validator;
        $this->embed = $embed;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $validator = $this->validator;
        $fieldTypes = ProfileType::$supportableFields;

        foreach ($this->profiles as $profile) {
            $fieldData = array('field' => 'text');
            if (isset($fieldTypes[$profile->getFieldType()])) {
                $fieldData = $fieldTypes[$profile->getFieldType()];
            }

            $validators = array();
            if (isset($fieldData['validators'])) {
                $validators = $fieldData['validators'];
            }

            $path = $this->embed ?  $profile->getName() : 'profile.'.$profile->getName();
            $fieldParameters = $this->buildFieldParameters(array('property_path' => $path, 'validator' => $validators), $profile);

            $builder->add($profile->getName(), isset($fieldData['field']) ? $fieldData['field'] : $profile->getFieldType(), $fieldParameters);
        }
    }

    public function buildFieldParameters($fieldParameters, $profile)
    {
        if ('list' === $profile->getFieldType()) {
            $fieldParameters['choices'] = $profile->getList(true);
        } else {
            $fieldParameters += $profile->getFieldParameters();
        }

        if ($profile->getLabel()) {
            $fieldParameters['label'] = $profile->getLabel();
        }

        if ($profile->getHelp()) {
            $fieldParameters['help'] = $profile->getHelp();
        }

        $fieldParameters['required'] = $profile->getIsRequired();
        if ($profile->getIsRequired()) {
            $fieldParameters['validator']['NotBlank'] = array();
        }

        return $fieldParameters;
    }

    public function getName()
    {
        return 'societo_base_member_profile';
    }
}
