<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Fixtures;

use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Societo\AuthenticationBundle\Entity\Account;
use Societo\BaseBundle\Entity\Member;

class LoadAccountData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load($manager)
    {
        $this->createAccount($manager, $this->createMember('example', true));

        $manager->flush();
    }

    protected function createAccount($manager, $member)
    {
        $manager->persist($member);

        $account = new Account('societo.base.test.user_provider', $member->getDisplayName(), $member);
        $manager->persist($account);
    }

    protected function createMember($name, $isAdmin = false)
    {
        $member = new Member();
        $member->setDisplayName($name);
        $member->setIsAdmin($isAdmin);

        if ($isAdmin) {
            $this->addReference('admin', $member);
        }

        return $member;
    }

    public function getOrder()
    {
        return 1;
    }
}
