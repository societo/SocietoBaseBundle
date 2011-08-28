<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Societo\AuthenticationBundle\Entity\Account;
use Societo\PageBundle\Entity\PageGadget;
use Societo\PageBundle\Entity\Page;
use Societo\BaseBundle\Entity\Profile;

class LoadEditProfileFormGadgetData implements FixtureInterface
{
    public function load($manager)
    {
        $page = new Page('example');
        $manager->persist($page);

        $gadget = new PageGadget($page, 'head', 'SocietoBaseBundle:EditProfileForm');
        $manager->persist($gadget);

        $profile = new Profile();
        $profile->setName('hobby');
        $profile->setIsRequired(true);
        $manager->persist($profile);

        $profile = new Profile();
        $profile->setName('nickname');
        $manager->persist($profile);

        $manager->flush();
    }
}
