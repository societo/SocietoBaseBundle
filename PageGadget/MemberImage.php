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
 * Gadget for member image block.
 *
 * @author Kousuke Ebihara <ebihara@php.net>
 */
class MemberImage extends AbstractPageGadget
{
    const SHOW_NOTHING = 'nothing';
    const SHOW_WITH_NAME = 'with_name';
    const SHOW_WITH_LINKED_NAME = 'with_linked_name';

    protected $caption = 'Member Image';

    protected $description = 'A block of specified member image';

    /**
     * {@inheritDoc}
     */
    public function execute($gadget, $parentAttributes, $parameters)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $member = $parentAttributes->get('member');
        if (!$member) {
            $member = $user->getMember();
        }

        $em = $this->getDoctrine()->getEntityManager();
        $followable = false;
        if ($this->get('security.context')->isGranted('ROLE_USER')) {
            $followable = $em->getRepository('SocietoRelationshipBundle:Follower')->isFollowable($user->getMemberId(), $member->getId());
        }
        $form = $this->createForm('form');

        return $this->render('SocietoBaseBundle:PageGadget:member_image.html.twig', array(
            'gadget' => $gadget,
            'member' => $member,
            'show_with_name' => $gadget->getParameter('show_with_name', self::SHOW_NOTHING),
            'follow_form' => $form->createView(),
            'followable' => $followable,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return array(
            'show_with_name' => array(
                'type' => 'choice',
                'options' => array(
                    'choices' => array(
                        self::SHOW_NOTHING => 'Show only image',
                        self::SHOW_WITH_NAME => 'Show image with username',
                        self::SHOW_WITH_LINKED_NAME => 'Show image with linked username',
                    ),
                ),
            ),
        );
    }
}
