<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Societo\PageBundle\Entity\PageGadget;
use Societo\PageBundle\Entity\Page;

class LoadMemberImageFormGadgetData implements FixtureInterface
{
    public function load($manager)
    {
        $page = new Page('example');
        $manager->persist($page);

        $gadget = new PageGadget($page, 'head', 'SocietoBaseBundle:MemberImageForm');
        $manager->persist($gadget);

        $manager->flush();
    }
}
