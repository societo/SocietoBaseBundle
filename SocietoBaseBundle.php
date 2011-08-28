<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class SocietoBaseBundle extends Bundle
{
    public function boot()
    {
        $dispatcher = $this->container->get('event_dispatcher');
        $securityContext = $this->container->get('security.context');

        $dispatcher->addListener('onSocietoRoutingParameterBuild', function($event) {
            $event->getManager()
                ->setParameter('member')
            ;
        });

        $dispatcher->addListener('onSocietoMatchedRouteParameterFilter', function ($event) use ($securityContext) {
            $parameters = $event->getParameters();
            $em = $event->getEntityManager();

            if (isset($parameters['member'])) {
                $parameters['member'] = $em->getRepository('SocietoBaseBundle:Member')->find($parameters['member']);
                if (!$parameters['member']) {
                    throw new \Exception('Member not found');
                }
            } else {
                $token = $securityContext->getToken();
                if ($token && $securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
                    $parameters['member'] = $token->getUser()->getMember();
                } else {
                    // if this parameter has default NULL value, this entry should be remove for conviniense in action
                    unset($parameters['member']);
                }
            }

            $event->setParameters($parameters);
        });

        $dispatcher->addListener('onSocietoGeneratingRouteParameterFilter', function ($event) {
            $parameters = $event->getParameters();
            $em = $event->getEntityManager();

            if (isset($parameters['member'])) {
                $parameters['member'] = $parameters['member']->getId();
            }

            $event->setParameters($parameters);
        });
    }
}
