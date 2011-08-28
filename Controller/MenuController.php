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

class MenuController extends Controller
{
    public function showAction()
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $menu = $em->getRepository('SocietoBaseBundle:Menu')->findOneBy(array(
            'name' => 'global',
        ));

        return $this->render('SocietoBaseBundle:Menu:show.html.twig', array('menu' => $menu));
    }
}
