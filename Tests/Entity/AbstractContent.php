<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Entity;

use Societo\BaseBundle\Entity\AbstractContent as AbstractContentEntity;
use Societo\BaseBundle\Entity\Member;
use Societo\BaseBundle\Test\EntityTestCase;

if (!class_exists('Societo\BaseBundle\Tests\Entity\ConcreteAbstractContent')) {
    class ConcreteAbstractContent extends \Societo\BaseBundle\Entity\AbstractContent
    {
        public function setBody($body)
        {
            $this->body = $body;
        }

        public function setAuthor($author)
        {
            $this->author = $author;
        }
    }
}

class AbstractContent extends EntityTestCase
{
    public function testGetBody()
    {
        $entity = new ConcreteAbstractContent();
        $this->assertEquals(null, $entity->getBody());
        $entity->setBody('body');
        $this->assertEquals('body', $entity->getBody());
    }

    public function testToString()
    {
        $entity = new ConcreteAbstractContent();
        $this->assertEquals('', (string)$entity);
        $entity->setBody('body');
        $this->assertEquals('body', (string)$entity);
    }

    public function testGetAuthor()
    {
        $entity = new ConcreteAbstractContent();
        $this->assertEquals(null, $entity->getAuthor());

        $member = new Member();
        $entity->setAuthor($member);
        $this->assertSame($member, $entity->getAuthor());
    }
}
