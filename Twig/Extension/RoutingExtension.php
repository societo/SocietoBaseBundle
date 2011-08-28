<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Twig\Extension;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class RoutingExtension extends \Twig_Extension
{
    private $generator, $request;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function getName()
    {
        return 'societo_routing';
    }

    public function getFunctions()
    {
        return array(
            'current_path' => new \Twig_Function_Method($this, 'generatePathByCurrentRoute'),
            'current_url' => new \Twig_Function_Method($this, 'generateUrlByCurrentRoute'),
            'current_route_info' => new \Twig_Function_Method($this, 'getCurrentRouteInfo'),
            'current_based_path' => new \Twig_Function_Method($this, 'generatePathByBasingCurrentRoute'),
        );
    }

    public function getCurrentRouteInfo($attributes = array(), $parameters = array())
    {
        if ($attributes instanceof \Symfony\Component\HttpFoundation\ParameterBag) {
            $attributes = $attributes->all();
        }

        if (!isset($attributes['_route'])) {
            throw new \Exception('_route is mandatory parameter');
        }

        if ($parameters instanceof \Symfony\Component\HttpFoundation\ParameterBag) {
            $parameters = $parameters->all();
        }

        $parameters = array_merge($parameters, $attributes);
        $routeName = $parameters['_route'];
        unset($parameters['_route']);

        return array(
            'routeName'   => $routeName,
            'routeParams' => $parameters,
        );
    }

    public function generatePathByCurrentRoute($attributes = array(), $parameters = array(), $absolute = false)
    {
        $info = $this->getCurrentRouteInfo($attributes, $parameters);

        return $this->generator->generate($info['routeName'], $info['routeParams'], $absolute);
    }

    public function generateUrlByCurrentRoute($attributes = array(), $parameters = array())
    {
        return $this->generatePathByCurrentRoute($attributes, $parameters);
    }

    public function generatePathByBasingCurrentRoute($route, $attributes = array(), $parameters = array(), $absolute = false)
    {
        try {
            return $this->generator->generate($route, $parameters, $absolute);
        } catch (\Exception $e) {
        }

        if ($attributes instanceof \Symfony\Component\HttpFoundation\ParameterBag) {
            $attributes = $attributes->all();
        }

        $attributes['_route'] = $route;

        $info = $this->getCurrentRouteInfo($attributes, $parameters);

        return $this->generator->generate($info['routeName'], $info['routeParams'], $absolute);
    }
}
