<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Societo\PageBundle\Entity\PageGadget;
use Societo\PageBundle\Entity\Page;

use Societo\BaseBundle\PageGadget\MemberImage;

class LoadMemberImageGadgetData implements FixtureInterface
{
    public function load($manager)
    {
        $page = new Page('example');
        $manager->persist($page);

        $gadgetName = 'SocietoBaseBundle:MemberImage';

        $manager->persist(new PageGadget($page, 'head', $gadgetName, ''));
        $manager->persist(new PageGadget($page, 'head', $gadgetName, '', array('show_with_name' => MemberImage::SHOW_NOTHING)));
        $manager->persist(new PageGadget($page, 'head', $gadgetName, '', array('show_with_name' => MemberImage::SHOW_WITH_NAME)));
        $manager->persist(new PageGadget($page, 'head', $gadgetName, '', array('show_with_name' => MemberImage::SHOW_WITH_LINKED_NAME)));

        $manager->flush();
    }
}
