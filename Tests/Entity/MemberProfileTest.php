<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Entity;

use Societo\BaseBundle\Entity\MemberProfile as MemberProfileEntity;
use Societo\BaseBundle\Entity\Member;
use Societo\BaseBundle\Entity\Profile;
use Societo\BaseBundle\Test\EntityTestCase;

class MemberProfileTest extends EntityTestCase
{
    public function createTestEntityManager($entityPaths = array())
    {
        if (!$entityPaths) {
            $entityPaths = $this->createClassFileDirectoryPaths(array(
                'Societo\BaseBundle\Entity\MemberProfile',
            ));
        }

        return parent::createTestEntityManager($entityPaths);
    }

    public function createMember($name)
    {
        $profile = new Member();
        $profile->setDisplayName($name);

        return $profile;
    }

    public function createProfile($name, $field = 'text', $label = '')
    {
        $profile = new Profile();
        $profile->setName($name);
        $profile->setFieldType($field);
        $profile->setLabel($label);

        return $profile;
    }

    private function createMemberAndProfiles($em)
    {
        $data = array();

        $em->persist($data['member1'] = $this->createMember('Kousuke Ebihara'));

        $em->persist($data['hobby'] = $this->createProfile('hobby'));
        $em->persist($data['intro'] = $this->createProfile('self_introduction', 'long text', 'Self Introduction'));
        $em->persist($data['age'] = $this->createProfile('age', 'integer'));
        $em->persist($data['gender'] = $this->createProfile('gender', 'list'));
        $em->persist($data['wed'] = $this->createProfile('wedding_anniversary', 'date'));
        $em->persist($data['birth'] = $this->createProfile('birthday', 'birthday'));
        $em->persist($data['fun'] = $this->createProfile('fun', 'boolean'));

        return $data;
    }

    public function testConstructor()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);
        $data = $this->createMemberAndProfiles($em);

        $memberProfile = new MemberProfileEntity($data['member1'], $data['hobby'], 'Swimming');
        $em->persist($memberProfile);

        $em->flush();

        $memberProfile = $em->getRepository('Societo\BaseBundle\Entity\MemberProfile')->find(1);
        $this->assertEquals('Swimming', $memberProfile->getValue());
    }

    public function testSetGetMember()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);
        $data = $this->createMemberAndProfiles($em);

        $memberProfile = new MemberProfileEntity(new Member(), $data['hobby'], 'Swimming');
        $memberProfile->setMember($data['member1']);
        $em->persist($memberProfile);

        $em->flush();

        $memberProfile = $em->getRepository('Societo\BaseBundle\Entity\MemberProfile')->find(1);
        $this->assertSame($data['member1'], $memberProfile->getMember());
    }

    public function testSetGetProfile()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);
        $data = $this->createMemberAndProfiles($em);

        $memberProfile = new MemberProfileEntity($data['member1'], $data['hobby'], 'Swimming');
        $memberProfile->setProfile($data['birth']);
        $em->persist($memberProfile);

        $em->flush();

        $memberProfile = $em->getRepository('Societo\BaseBundle\Entity\MemberProfile')->find(1);
        $this->assertSame($data['birth'], $memberProfile->getProfile());
    }

    public function testSetGetValue()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);
        $data = $this->createMemberAndProfiles($em);

        $em->persist(new MemberProfileEntity($data['member1'], $data['hobby'], 'Swimming'));
        $em->persist(new MemberProfileEntity($data['member1'], $data['intro'], 'Hello'));
        $em->persist(new MemberProfileEntity($data['member1'], $data['age'], 23));
        $em->persist(new MemberProfileEntity($data['member1'], $data['gender'], 'Masculine'));
        $em->persist(new MemberProfileEntity($data['member1'], $data['wed'], new \DateTime('1988-04-23')));
        $em->persist(new MemberProfileEntity($data['member1'], $data['birth'], new \DateTime('1988-04-23')));
        $em->persist(new MemberProfileEntity($data['member1'], $data['fun'], true));

        $em->flush();

        $memberProfile = $em->getRepository('Societo\BaseBundle\Entity\MemberProfile')->find(1);
        $this->assertEquals('Swimming', $memberProfile->getValue());

        $memberProfile = $em->getRepository('Societo\BaseBundle\Entity\MemberProfile')->find(2);
        $this->assertEquals('Hello', $memberProfile->getValue());

        $memberProfile = $em->getRepository('Societo\BaseBundle\Entity\MemberProfile')->find(3);
        $this->assertEquals(23, $memberProfile->getValue());

        $memberProfile = $em->getRepository('Societo\BaseBundle\Entity\MemberProfile')->find(4);
        $this->assertEquals('Masculine', $memberProfile->getValue());

        $memberProfile = $em->getRepository('Societo\BaseBundle\Entity\MemberProfile')->find(5);
        $this->assertEquals('1988-04-23', $memberProfile->getValue()->format('Y-m-d'));

        $memberProfile = $em->getRepository('Societo\BaseBundle\Entity\MemberProfile')->find(6);
        $this->assertEquals('1988-04-23', $memberProfile->getValue()->format('Y-m-d'));

        $memberProfile = $em->getRepository('Societo\BaseBundle\Entity\MemberProfile')->find(7);
        $this->assertTrue($memberProfile->getValue());
    }
}
