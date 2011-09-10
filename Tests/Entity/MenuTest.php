<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Entity;

use Societo\BaseBundle\Entity\Menu as MenuEntity;
use Societo\BaseBundle\Test\EntityTestCase;

class MenuTest extends EntityTestCase
{
    public function createTestEntityManager($entityPaths = array())
    {
        if (!$entityPaths) {
            $entityPaths = $this->createClassFileDirectoryPaths(array(
                'Societo\BaseBundle\Entity\Menu',
            ));
        }

        return parent::createTestEntityManager($entityPaths);
    }

    public function testConstructor()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $em->persist(new MenuEntity('A'));
        $em->flush();

        $menu = $em->getRepository('Societo\BaseBundle\Entity\Menu')->findOneBy(array('name' => 'A'));
        $this->assertEquals(array(), $menu->getParameters());
    }

    public function testAddUri()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $menu = new MenuEntity('A');
        $menu->addUri('Example', 'http://example.com/');
        $em->persist($menu);
        $em->flush();

        $menu = $em->getRepository('Societo\BaseBundle\Entity\Menu')->findOneBy(array('name' => 'A'));
        $this->assertEquals(array('Example' => array('uri' => 'http://example.com/')), $menu->getParameters());
    }

    public function testAddRoute()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $menu = new MenuEntity('A');
        $params = array('id' => 1);
        $menu->addRoute('Example', 'example', $params);
        $em->persist($menu);
        $em->flush();

        $menu = $em->getRepository('Societo\BaseBundle\Entity\Menu')->findOneBy(array('name' => 'A'));
        $this->assertEquals(array('Example' => array('route' => 'example', 'parameters' => $params)), $menu->getParameters());
    }

    public function testHasGetRemoveItem()
    {
        $em = $this->createTestEntityManager();
        $this->rebuildDatabase($em);

        $menu = new MenuEntity('A');
        $menu->addRoute('Example', 'example');
        $menu->addUri('Example2', 'http://example.com/');
        $em->persist($menu);
        $em->flush();

        $menu = $em->getRepository('Societo\BaseBundle\Entity\Menu')->findOneBy(array('name' => 'A'));
        $this->assertTrue($menu->hasItem('Example'));
        $this->assertTrue($menu->hasItem('Example2'));
        $this->assertFalse($menu->hasItem('Example3'));

        $this->assertEquals(array('route' => 'example', 'parameters' => array()), $menu->getItem('Example'));
        $this->assertEquals(array('uri' => 'http://example.com/'), $menu->getItem('Example2'));
        try {
            $menu->getItem('Example3');
            $this->fail();
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof \OutOfBoundsException);
        }

        $menu->removeItem('Example');
        $menu->removeItem('Example2');
        $menu->removeItem('Example3');

        $this->assertFalse($menu->hasItem('Example'));
        $this->assertFalse($menu->hasItem('Example2'));
        $this->assertFalse($menu->hasItem('Example3'));
    }


}
