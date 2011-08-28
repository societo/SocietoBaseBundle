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

class SearchMemberProfileType extends AbstractType
{
    private $em, $profiles, $validator;

    public function __construct(EntityManager $em, $profiles)
    {
        $this->em = $em;
        $this->profiles = $profiles;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $fieldTypes = ProfileType::$supportableFields;

        foreach ($this->profiles as $profile) {
            $fieldData = array('field' => 'text');
            if (isset($fieldTypes[$profile->getFieldType()])) {
                $fieldData = $fieldTypes[$profile->getFieldType()];
            }

            // TODO
            if ($profile->isDateTimeValue()) {
                continue;
            }

            $fieldParameters = array('required' => false);
            if ('list' === $profile->getFieldType()) {
                $fieldParameters['choices'] = $profile->getList(true);
            } else {
                $fieldParameters += $profile->getFieldParameters();
            }

            if ('long text' === $profile->getFieldType()) {
                $fieldData['field'] = 'text';
            } elseif ('boolean' === $profile->getFieldType()) {
                $fieldData['field'] = 'choice';
                $fieldParameters['choices'] = array('0' => 'No', '1' => 'Yes');
            }

            $builder->add($profile->getName(), isset($fieldData['field']) ? $fieldData['field'] : $profile->getFieldType(), $fieldParameters);
        }
    }

    public function getName()
    {
        return 'profile';
    }

    public function getDefaultOptions(array $options)
    {
        $result = parent::getDefaultOptions($options);

        $result['csrf_protection'] = false;

        return $result;
    }
}
