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

class ImageExtension extends \Twig_Extension
{
    private $generator;

    public function __construct(UrlGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function getName()
    {
        return 'societo_image';
    }

    public function getFunctions()
    {
        return array(
            'image_path' => new \Twig_Function_Method($this, 'getImagePath'),
        );
    }

    public function getImagePath($filename = null, $size = 'raw', $default = 'default')
    {
        $route = 'show_image';
        $parameters = array(
            'filename' => $filename ? $filename : $default,
        );

        if ('raw' !== $size) {
            $route = 'show_resized_image';
            list($w, $h) = array_merge((array)explode('x', $size, 2), array('0', '0'));

            $parameters['width']  = $w;
            $parameters['height'] = $h;
        }

        if (is_object($parameters['filename'])) {
            $parameters['filename'] = $parameters['filename']->getFilename();
        }

        return $this->generator->generate($route, $parameters, false);
    }
}
