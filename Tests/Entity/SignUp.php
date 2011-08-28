<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Entity;

use Societo\BaseBundle\Entity\SignUp as SignUpEntity;
use Societo\BaseBundle\Test\EntityTestCase;

class SignUp extends EntityTestCase
{
    public function createTestEntityManager($entityPaths = array())
    {
        if (!$entityPaths) {
            $entityPaths = $this->createClassFileDirectoryPaths(array(
                'Societo\BaseBundle\Entity\SignUp',
            ));
        }

        return parent::createTestEntityManager($entityPaths);
    }

    public function testConstructor()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $parameters = array('A' => 'B');
        $em->persist(new SignUpEntity('namespace', 'co3k', $parameters));
        $em->flush();

        $signUp = $em->getRepository('Societo\BaseBundle\Entity\SignUp')->find(1);
        $this->assertEquals('namespace', $signUp->getNamespace());
        $this->assertEquals('co3k', $signUp->getUsername());
        $this->assertEquals($parameters, $signUp->getParameters());
    }

    public function testSetGetUsername()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $signUp = new SignUpEntity('namespace', 'co3k');
        $signUp->setUsername($signUp->getUsername().'!');
        $em->persist($signUp);
        $em->flush();

        $signUp = $em->getRepository('Societo\BaseBundle\Entity\SignUp')->find(1);
        $this->assertEquals('co3k!', $signUp->getUsername());
    }

    public function testSetGetParameters()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $parameters = array('A' => 'B');

        $signUp = new SignUpEntity('namespace', 'co3k');
        $signUp->setParameters($parameters);
        $em->persist($signUp);
        $em->flush();

        $signUp = $em->getRepository('Societo\BaseBundle\Entity\SignUp')->find(1);
        $this->assertEquals($parameters, $signUp->getParameters());
    }

    public function testIsAdmin()
    {
        $signUp = new SignUpEntity('namespace', 'co3k');
        $this->assertFalse($signUp->isAdmin());
    }

    public function testGetLastLogin()
    {
        $signUp = new SignUpEntity('namespace', 'co3k');
        $this->assertFalse($signUp->getLastLogin());
    }
}
