<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\PageGadget;

use \Societo\PageBundle\PageGadget\AbstractPageGadget;

/**
 * Gadget for edit profile form.
 *
 * @author Kousuke Ebihara <ebihara@php.net>
 */
class EditProfileForm extends AbstractPageGadget
{
    public $caption = 'Edit profile form';

    public $description = 'Show a form for modify current member\'s profile';

    /**
     * {@inheritDoc}
     */
    public function execute($gadget, $parentAttributes, $parameters)
    {
        $user = $this->get('security.context')->getToken()->getUser();

        $em = $this->get('doctrine.orm.entity_manager');
        $profiles = $em->getRepository('SocietoBaseBundle:Profile')->findAll();

        if (isset($parameters['form'])) {
            $form = $parameters['form'];
        } else {
            $form = $this->get('form.factory')
                ->create(new \Societo\BaseBundle\Form\MemberProfileType($em, $profiles, $this->get('validator')));
            $form->setData($user->getMember());
        }

        return $this->render('SocietoBaseBundle:PageGadget:edit_profile_form.html.twig', array(
            'gadget' => $gadget,
            'form'   => $form->createView(),
        ));
    }
}
