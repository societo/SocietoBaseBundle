<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Societo\PageBundle\Entity\Page;
use Societo\BaseBundle\Entity\Menu;

class LoadMenuData extends AbstractFixture
{
    public function load($manager)
    {
        $page = new Page('public_example');
        $manager->persist($page);

        $menu = new Menu('global');
        $menu->addRoute('Login', 'public_example');
        $menu->addRoute('Member', 'profile', array(
            'example' => 1,
        ));
        $menu->addUri('Societo', 'http://societo.org/');
        $manager->persist($menu);

        $manager->flush();
    }
}
