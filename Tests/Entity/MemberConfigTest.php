<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Entity;

use Societo\BaseBundle\Entity\MemberConfig as MemberConfigEntity;
use Societo\BaseBundle\Entity\Member;
use Societo\BaseBundle\Test\EntityTestCase;

class MemberConfigTest extends EntityTestCase
{
    public function createTestEntityManager($entityPaths = array())
    {
        if (!$entityPaths) {
            $entityPaths = $this->createClassFileDirectoryPaths(array(
                'Societo\BaseBundle\Entity\MemberConfig',
                'Societo\BaseBundle\Entity\Member',
            ));
        }

        return parent::createTestEntityManager($entityPaths);
    }

    public function testConstructor()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $member = new Member();
        $member->setDisplayName('A');
        $em->persist($member);

        $em->persist(new MemberConfigEntity($member, __FILE__, 'config1', 'value1'));
        $em->flush();

        $entity = $em->getRepository('Societo\BaseBundle\Entity\MemberConfig')->find(1);
        $this->assertEquals(__FILE__, $entity->getNamespace());
        $this->assertEquals('value1', $entity->getValue());
        $this->assertEquals('value1', (string)$entity);
        $this->assertSame($member, $entity->getMember());
    }

    public function testSetGetValue()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $member = new Member();
        $member->setDisplayName('A');
        $em->persist($member);

        $entity = new MemberConfigEntity($member, __FILE__, 'config1', 'value1');
        $entity->setValue($entity->getValue().'!');

        $em->persist($entity);
        $em->flush();

        $entity = $em->getRepository('Societo\BaseBundle\Entity\MemberConfig')->find(1);
        $this->assertEquals('value1!', $entity->getValue());
    }

    public function testDenyDuplicateAccount()
    {
        $this->setExpectedException('PDOException');

        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $member = new Member();
        $member->setDisplayName('A');
        $em->persist($member);

        $entity = new MemberConfigEntity($member, __FILE__, 'config1', 'value1');
        $em->persist($entity);
        $em->flush();

        $entity = new MemberConfigEntity($member, __FILE__, 'config1', 'value1');
        $em->persist($entity);
        $em->flush();
    }
}
