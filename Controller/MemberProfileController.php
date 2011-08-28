<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\TemplateReference;
use Societo\BaseBundle\Util\ArrayAccessibleParameterBag;

use Societo\PageBundle\Entity\PageGadget;

/**
 * Controller for handling member profile actions.
 *
 * @author Kousuke Ebihara <ebihara@php.net>
 */
class MemberProfileController extends Controller
{
    /**
     * Update profile action.
     *
     * @param PageGadget $gadget
     * @return Response
     */
    public function updateAction(PageGadget $gadget)
    {
        $request = $this->get('request');
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine.orm.entity_manager');

        $profiles = $em->getRepository('SocietoBaseBundle:Profile')->findAll();
        $member = $user->getMember();

        $form = $this->get('form.factory')
            ->create(new \Societo\BaseBundle\Form\MemberProfileType($em, $profiles, $this->get('validator')));
        $form->setData($member);
        $form->bindRequest($request);

        if ($form->isValid()) {
            $member = $form->getData();
            foreach ($member->getProfile() as $key => $value) {
                $memberProfile = $member->getRawProfile($key, true);
                if (!$memberProfile) {
                    $profile = $em->getRepository('SocietoBaseBundle:Profile')->findOneBy(array('name' => $key));
                    $memberProfile = new \Societo\BaseBundle\Entity\MemberProfile($member, $profile);
                }
                $memberProfile->setValue($value);

                $em->persist($memberProfile);
            }

            $em->flush();

            $this->get('session')->setFlash('success', 'Changes are saved successfully');

            return $this->redirect($this->generateUrl('profile', array('member' => $member)));
        }

        return $this->render('SocietoPluginBundle:Gadget:only_gadget.html.twig', array(
            'gadget' => $gadget,
            'parent_attributes' => $this->get('request')->attributes,
            'parameters' => new ArrayAccessibleParameterBag(array('form' => $form)),
        ));
    }
}
