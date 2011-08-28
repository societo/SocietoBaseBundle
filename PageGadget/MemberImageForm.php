<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\PageGadget;

use Societo\PageBundle\PageGadget\AbstractPageGadget;
use Societo\BaseBundle\Form\MemberImageType;

/**
 * Gadget for member image form.
 *
 * @author Kousuke Ebihara <ebihara@php.net>
 */
class MemberImageForm extends AbstractPageGadget
{
    protected $caption = 'Member Image Form';

    protected $description = 'A form for member image';

    /**
     * {@inheritDoc}
     */
    public function execute($gadget, $parentAttributes, $parameters)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $member = $user->getMember();

        if (isset($parameters['form'])) {
            $form = $parameters['form'];
        } else {
            $form = $this->get('form.factory')->create(new MemberImageType());
        }

        return $this->render('SocietoBaseBundle:PageGadget:member_image_form.html.twig', array(
            'gadget' => $gadget,
            'form'   => $form->createView(),
            'member' => $member,
        ));
    }
}
