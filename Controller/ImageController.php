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
use Symfony\Component\Security\Core\SecurityContext;

use Imagine\Image\Box;

class ImageController extends Controller
{
    private function getImagePath($filename)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $pluginBundle = $this->get('kernel')->getBundle('SocietoPluginBundle');
        $imageBasePath = '/Resources/public/images';

        if ('member-default' === $filename) {
            return $pluginBundle->locateSkinPluginResource($imageBasePath.'/societo-human-default-image.png');
        } elseif ('default' === $filename) {
            return $pluginBundle->locateSkinPluginResource($imageBasePath.'/societo-gadget-default-image_004.png');
        }

        $file = $em->getRepository('SocietoUtilStorageBundle:File')->findOneBy(array(
            'filename' => $filename,
        ));
        $binary = $this->get('societo.storage')->restore($filename);

        $tmpInput = tempnam(sys_get_temp_dir(), 'SCIMG');
        file_put_contents($tmpInput, $binary);

        return $tmpInput;
    }

    public function showAction($filename, $width = null, $height = null)
    {
        if (!($width && $height)) {
            $width = $height = null;
        }

        // TODO
        $width = $height;
        $available = array(20, 25, 50, 100, 250);
        if (!in_array($width, $available)) {
            return new \Symfony\Component\HttpFoundation\Response('', 404);
        }

        $dir = sprintf($this->get('kernel')->getRootDir().'/../web/image/%dx%d/', $width, $height);
        $filesystem = $this->get('filesystem');
        $filesystem->mkdir($dir);
        $cachePath = $dir.$filename;
        if (is_file($cachePath)) {
            return new \Symfony\Component\HttpFoundation\Response(file_get_contents($cachePath), 200, array(
                'Content-Type' => 'image/png',
            ));
        }

        $imagine = new \Imagine\Gd\Imagine();
        $filepath = $this->getImagePath($filename);

        $image = $imagine->open($filepath);
        $image = $image->thumbnail(new Box($width, $width), \Imagine\Gd\Image::THUMBNAIL_OUTBOUND);

        if (false !== strpos($filepath, sys_get_temp_dir())) {
            unlink($filepath);
        }

        file_put_contents($cachePath, (string)$image);
        $response = new \Symfony\Component\HttpFoundation\Response(file_get_contents($cachePath), 200, array(
            'Content-Type' => 'image/png',
        ));

        $now = time();
        $response->setCache(array(
            'public'  => true,
            'max_age' => strtotime('+10seconds', $now) - $now,
        ));

        return $response;
    }
}
