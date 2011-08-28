<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Entity;

use Societo\BaseBundle\Test\EntityTestCase;

if (!class_exists('Societo\BaseBundle\Tests\Entity\ConcreteBaseEntity')) {
    class ConcreteBaseEntity extends \Societo\BaseBundle\Entity\BaseEntity
    {
        public function setId($id)
        {
            $this->id = $id;
        }

        public function setCreatedAt($time)
        {
            $this->createdAt = new \DateTime($time);
        }

        public function setUpdatedAt($time)
        {
            $this->updatedAt = new \DateTime($time);
        }
    }
}

class BaseEntity extends EntityTestCase
{
    public function testGetId()
    {
        $entity = new ConcreteBaseEntity();

        $this->assertEquals(null, $entity->getId());

        $entity->setId(1);
        $this->assertEquals(1, $entity->getId());
    }

    public function testIsNew()
    {
        $entity = new ConcreteBaseEntity();

        $this->assertTrue($entity->isNew());

        $entity->setId(1);
        $this->assertFalse($entity->isNew());
    }

    public function testGetCreatedAt()
    {
        $entity = new ConcreteBaseEntity();

        $this->assertEquals(null, $entity->getCreatedAt());

        $time = '1988-04-23 02:00:00';
        $entity->setCreatedAt($time);
        $this->assertEquals($time, $entity->getCreatedAt()->format('Y-m-d H:i:s'));
    }

    public function testGetUpdatedAt()
    {
        $entity = new ConcreteBaseEntity();

        $this->assertEquals(null, $entity->getUpdatedAt());

        $time = '1988-04-23 02:00:00';
        $entity->setUpdatedAt($time);
        $this->assertEquals($time, $entity->getUpdatedAt()->format('Y-m-d H:i:s'));
    }

    public function testModifyCreatedAt()
    {
        $entity = new ConcreteBaseEntity();
        $this->assertEquals(null, $entity->getCreatedAt());
        $this->assertEquals(null, $entity->getUpdatedAt());
        $entity->modifyCreatedAt();
        $this->assertTrue($entity->getCreatedAt() instanceof \DateTime);
        $this->assertTrue($entity->getUpdatedAt() instanceof \DateTime);
    }

    public function modifyUpdatedAt()
    {
        $entity = new ConcreteBaseEntity();
        $this->assertEquals(null, $entity->getCreatedAt());
        $this->assertEquals(null, $entity->getUpdatedAt());
        $entity->modifyCreatedAt();
        $this->assertEquals(null, $entity->getCreatedAt());
        $this->assertTrue($entity->getUpdatedAt() instanceof \DateTime);
    }
}
