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
use Societo\BaseBundle\Form\SearchMemberProfileType;

/**
 * Gadget for member search form.
 *
 * @author Kousuke Ebihara <ebihara@php.net>
 */
class MemberSearchForm extends AbstractPageGadget
{
    protected $caption = 'Member Search Form';

    protected $description = 'A block of member search form';

    /**
     * {@inheritDoc}
     */
    public function execute($gadget, $parentAttributes, $parameters)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $profiles = $em->getRepository('SocietoBaseBundle:Profile')->findAll();
        $form = $this->get('form.factory')
            ->create(new \Societo\BaseBundle\Form\SearchMemberProfileType($em, $profiles))
        ;
        $form->bindRequest($this->get('request'));

        return $this->render('SocietoBaseBundle:PageGadget:member_search_form.html.twig', array(
            'gadget'       => $gadget,
            'form'         => $form->createView(),
            'result_route' => $gadget->getParameter('result_route'),
            'attributes'   => $parentAttributes,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function getOptions()
    {
        return array(
            'result_route' => array(
                'type' => 'text',
                'options' => array(
                    'required' => false,
                ),
            ),
        );
    }
}
