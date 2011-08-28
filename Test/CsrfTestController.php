<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Test;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class CsrfTestController extends Controller
{
    public function renderAction()
    {
        $form = $this->createForm('form');

        return new Response($form['_token']->getData(), 200, array(
            'Content-Type' => 'text/plain',
        ));
    }
}
