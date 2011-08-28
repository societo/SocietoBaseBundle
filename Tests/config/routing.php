<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Routing\Route;
use Symfony\Component\Config\Resource\FileResource;

$collection = new RouteCollection();
$collection->addCollection($loader->import($_SERVER['SYMFONY__KERNEL__ROOT_DIR'].'/config/routing.yml'));

$collection->add('public_example', new Route('/public/example', array(
    '_controller' => 'SocietoPageBundle:Frontend:handle',
    'name' => 'public_example',
)));

$controller = '\Societo\PluginBundle\Test\OnlyGadgetTestController::renderAction';
$collection->add('gadget_test', new Route('/gadget_test/{gadget}', array(
    '_controller' => $controller,
)));

$collection->add('member_gadget_test', new Route('/member_gadget_test/{gadget}/{member}', array(
    '_controller' => $controller,
    'member' => null,
)));

$collection->add('profile', new Route('/member/{member}', array(
    'member' => null,
)));

return $collection;
