<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Entity;

use Societo\BaseBundle\Entity\Profile as ProfileEntity;
use Societo\BaseBundle\Test\EntityTestCase;

class ProfileTest extends EntityTestCase
{
    public function createTestEntityManager($entityPaths = array())
    {
        if (!$entityPaths) {
            $entityPaths = $this->createClassFileDirectoryPaths(array(
                'Societo\BaseBundle\Entity\Profile',
            ));
        }

        return parent::createTestEntityManager($entityPaths);
    }

    public function testSetGetName()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $profile = new ProfileEntity();
        $profile->setName('A');
        $em->persist($profile);
        $em->flush();

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(1);
        $this->assertEquals('A', $profile->getName());
    }

    public function testSetGetLabel()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $profile = new ProfileEntity();
        $profile->setName('A');
        $em->persist($profile);

        $profile = new ProfileEntity();
        $profile->setName('B');
        $profile->setLabel('Label');
        $em->persist($profile);
        $em->flush();

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(1);
        $this->assertEquals('', $profile->getLabel());
        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(2);
        $this->assertEquals('Label', $profile->getLabel());
    }

    public function testSetGetRequired()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $profile = new ProfileEntity();
        $profile->setName('A');
        $em->persist($profile);

        $profile = new ProfileEntity();
        $profile->setName('B');
        $profile->setIsRequired(true);
        $em->persist($profile);
        $em->flush();

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(1);
        $this->assertFalse($profile->getIsRequired());
        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(2);
        $this->assertTrue($profile->getIsRequired());
    }

    public function testSetGetFieldType()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $profile = new ProfileEntity();
        $profile->setName('A');
        $em->persist($profile);

        $profile = new ProfileEntity();
        $profile->setName('B');
        $profile->setFieldType('test_field_type');
        $em->persist($profile);
        $em->flush();

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(1);
        $this->assertEquals('text', $profile->getFieldType());
        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(2);
        $this->assertEquals('test_field_type', $profile->getFieldType());
    }

    public function testSetGetHasFieldParameters()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $profile = new ProfileEntity();
        $profile->setName('A');
        $profile->setFieldParameter('key1', 'value1');
        $em->persist($profile);
        $em->flush();

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(1);
        $this->assertTrue($profile->hasFieldParameter('key1'));
        $this->assertFalse($profile->hasFieldParameter('key2'));

        $this->assertEquals('value1', $profile->getFieldParameter('key1'));
        $this->assertEquals(null, $profile->getFieldParameter('key2'));
    }

    public function testGetFieldParameters()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $profile = new ProfileEntity();
        $profile->setName('A');
        $em->persist($profile);

        $profile = new ProfileEntity();
        $profile->setName('B');
        $profile->setFieldParameter('key1', 'value1');
        $em->persist($profile);
        $em->flush();

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(1);
        $this->assertEquals(array(), $profile->getFieldParameters());
        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(2);
        $this->assertEquals(array('key1' => 'value1'), $profile->getFieldParameters());
    }

    public function testSetGetList()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $list = "A\nB\nC\n\t\n \n D    ";

        $profile = new ProfileEntity();
        $profile->setName('A');
        $profile->setList($list);
        $em->persist($profile);

        $profile = new ProfileEntity();
        $profile->setName('B');
        $profile->setFieldType('list');
        $profile->setList($list);
        $em->persist($profile);
        $em->flush();

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(1);
        $this->assertEquals(null, $profile->getList());

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(2);
        $this->assertEquals(trim($list), $profile->getList());
        $this->assertEquals(array('A', 'B', 'C', 'D'), $profile->getList(true));
    }

    public function testSetGetHelp()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $profile = new ProfileEntity();
        $profile->setName('A');
        $em->persist($profile);

        $profile = new ProfileEntity();
        $profile->setName('B');
        $profile->setHelp('Help');
        $em->persist($profile);
        $em->flush();

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(1);
        $this->assertEquals(null, $profile->getHelp());

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(2);
        $this->assertEquals('Help', $profile->getHelp());
    }

    public function testIsDateTimeValue()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $profile = new ProfileEntity();
        $profile->setName('A');
        $profile->setFieldType('text');
        $em->persist($profile);

        $profile = new ProfileEntity();
        $profile->setName('B');
        $profile->setFieldType('date');
        $em->persist($profile);

        $profile = new ProfileEntity();
        $profile->setName('C');
        $profile->setFieldType('birthday');
        $em->persist($profile);

        $em->flush();

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(1);
        $this->assertFalse($profile->isDateTimeValue());

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(2);
        $this->assertTrue($profile->isDateTimeValue());

        $profile = $em->getRepository('Societo\BaseBundle\Entity\Profile')->find(3);
        $this->assertTrue($profile->isDateTimeValue());
    }
}
