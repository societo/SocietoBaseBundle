<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Sensio\Bundle\FrameworkExtraBundle\EventListener\ParamConverterListener as BaseParamConverterListener;

class ParamConverterListener extends BaseParamConverterListener
{
    public function onKernelController(FilterControllerEvent $event)
    {
        // avoid SencioFrameworkExtraBundle bug
        $_event = clone $event;
        $controller = $_event->getController();

        if (!is_array($controller) && is_callable(array($controller, '__invoke'))) {
            $controller = array($controller, '__invoke');
        }

        $_event->setController($controller);

        parent::onKernelController($_event);
    }
}
