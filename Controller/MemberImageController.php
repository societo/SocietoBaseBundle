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
use Societo\BaseBundle\Form\MemberImageType;
use Societo\Util\StorageBundle\Entity\File;

use Symfony\Component\Finder\Finder;

class MemberImageController extends Controller
{
    public function postAction($gadget)
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $em = $this->get('doctrine.orm.entity_manager');
        $request = $this->get('request');
        $storage = $this->get('societo.storage');

        $form = $this->get('form.factory')->create(new MemberImageType(), new File());
        $form->bindRequest($request);
        if ($form->isValid()) {
            $file = $form->getData();
            $file->setRandomizedFilename();

            $em->getRepository('SocietoBaseBundle:MemberImage')
                ->setMemberImage($user->getMember(), $storage->storeFromEntity($file));

            // TODO: extract
            $dir = $this->get('kernel')->getRootDir().'/../web/image/member';
            $filesystem = $this->get('filesystem');
            $finder = new Finder();
            $filesystem->remove($finder->files()->name($user->getMemberId())->in($dir));

            $this->get('session')->setFlash('success', 'Changes are saved successfully');

            return $this->redirect($this->generateUrl('profile', array('member' => $user->getMember())));
        }

        return $this->render('SocietoPluginBundle:Gadget:only_gadget.html.twig', array(
            'gadget' => $gadget,
            'parent_attributes' => $this->get('request')->attributes,
            'parameters' => new ArrayAccessibleParameterBag(array('form' => $form)),
        ));
    }

    public function showAction($member, $width = null, $height = null)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $image = $em->getRepository('SocietoBaseBundle:MemberImage')->findOneBy(array(
            'member' => $member->getId(),
        ));

        $filename = ($image) ? $image->getFilename() : 'member-default';

        $dir = sprintf($this->get('kernel')->getRootDir().'/../web/image/member/%dx%d/', $width, $height);
        $path = $dir.$member->getId();

        if (is_file($path)) {
            if (!$image || filemtime($path) > $image->getUpdatedAt()->format('U')) {
                return new \Symfony\Component\HttpFoundation\Response(file_get_contents($path), 200, array(
                    'Content-Type' => 'image/png',
                ));
            }
        }

        $response = $this->get('societo.base.controller.image')->showAction($filename, $width, $height);
        if ($response->isSuccessful()) {
            $filesystem = $this->get('filesystem');
            $filesystem->mkdir($dir);

            file_put_contents($path, $response->getContent());
        }

        return $response;
    }
}
