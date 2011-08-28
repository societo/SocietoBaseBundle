<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Societo\AuthenticationBundle\Entity\Account;

use Societo\PageBundle\Entity\PageGadget;
use Societo\PageBundle\Entity\Page;
use Societo\BaseBundle\Entity\Profile;
use Societo\BaseBundle\Entity\MemberProfile;
use Societo\BaseBundle\Entity\Member;

class LoadMemberProfileListGadgetData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load($manager)
    {
        $page = new Page('example');
        $manager->persist($page);

        $gadget = new PageGadget($page, 'head', 'SocietoBaseBundle:MemberProfileList');
        $manager->persist($gadget);

        $manager->persist($hobby = $this->createProfile('hobby'));
        $manager->persist($intro = $this->createProfile('self_introduction', 'long text', 'Self Introduction'));
        $manager->persist($age = $this->createProfile('age', 'integer'));
        $manager->persist($gender = $this->createProfile('gender', 'list'));
        $manager->persist($wed = $this->createProfile('wedding_anniversary', 'date'));
        $manager->persist($birth = $this->createProfile('birthday', 'birthday'));
        $manager->persist($fun = $this->createProfile('fun', 'boolean'));

        $admin = $this->getReference('admin');
        $manager->persist(new MemberProfile($admin, $hobby, 'Swimming'));
        $manager->persist(new MemberProfile($admin, $intro, "Hi!\nI'm administrator of this site.\nEnjoy!"));
        $manager->persist(new MemberProfile($admin, $age, '23'));
        $manager->persist(new MemberProfile($admin, $gender, 'Masculine'));
        $manager->persist(new MemberProfile($admin, $wed, '2011-08-07 00:00:00'));
        $manager->persist(new MemberProfile($admin, $birth, '1988-04-23 00:00:00'));
        $manager->persist(new MemberProfile($admin, $fun, '1'));

        $member = new Member();
        $member->setDisplayName('example2');
        $manager->persist($member);

        $memberProfile = new MemberProfile($member, $hobby, 'Eating');
        $manager->persist($memberProfile);

        $manager->flush();
    }

    public function createProfile($name, $field = 'text', $label = '')
    {
        $profile = new Profile();
        $profile->setName($name);
        $profile->setFieldType($field);
        $profile->setLabel($label);

        return $profile;
    }

    public function getOrder()
    {
        return PHP_INT_MAX;
    }
}
