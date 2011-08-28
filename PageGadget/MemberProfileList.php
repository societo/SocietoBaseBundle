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
 * Gadget for member profile list.
 *
 * @author Kousuke Ebihara <ebihara@php.net>
 */
class MemberProfileList extends AbstractPageGadget
{
    public $caption = 'Member\'s Profile List';

    public $description = 'A list of specified member\'s profile';

    /**
     * {@inheritDoc}
     */
    public function execute($gadget, $parentAttributes, $parameters)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $member = $parentAttributes->get('member', $this->get('security.context')->isGranted('ROLE_USER') ? $user->getMember() : null);
        if (!$member) {
            throw $this->createNotFoundException();
        }

        $profiles = $this->get('doctrine.orm.entity_manager')->getRepository('SocietoBaseBundle:MemberProfile')->findBy(array(
            'member' => $member->getId(),
        ));

        return $this->render('SocietoBaseBundle:PageGadget:member_profile_list.html.twig', array(
            'gadget'  => $gadget,
            'profiles' => $profiles,
        ));
    }
}
