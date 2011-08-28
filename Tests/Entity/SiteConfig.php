<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Entity;

use Societo\BaseBundle\Entity\SiteConfig as SiteConfigEntity;
use Societo\BaseBundle\Test\EntityTestCase;

class SiteConfig extends EntityTestCase
{
    public function createTestEntityManager($entityPaths = array())
    {
        if (!$entityPaths) {
            $entityPaths = $this->createClassFileDirectoryPaths(array(
                'Societo\BaseBundle\Entity\SiteConfig',
            ));
        }

        return parent::createTestEntityManager($entityPaths);
    }

    public function testConstructor()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $em->persist(new SiteConfigEntity('key1', 'value1'));
        $em->flush();

        $config = $em->getRepository('Societo\BaseBundle\Entity\SiteConfig')->find(1);
        $this->assertEquals('key1', $config->getName());
        $this->assertEquals('value1', $config->getValue());
    }

    public function testSetGetName()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $config = new SiteConfigEntity('key1', 'value1');
        $config->setName($config->getName().'!');
        $em->persist($config);
        $em->flush();

        $config = $em->getRepository('Societo\BaseBundle\Entity\SiteConfig')->find(1);
        $this->assertEquals('key1!', $config->getName());
    }

    public function testSetGetValue()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $config = new SiteConfigEntity('key1', 'value1');
        $config->setValue($config->getValue().'!');
        $em->persist($config);
        $em->flush();

        $config = $em->getRepository('Societo\BaseBundle\Entity\SiteConfig')->find(1);
        $this->assertEquals('value1!', $config->getValue());
    }
}
