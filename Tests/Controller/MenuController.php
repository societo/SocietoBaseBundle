<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Controller;

use Societo\BaseBundle\Test\WebTestCase;

class MenuController extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(array(
            'Societo\BaseBundle\Tests\Fixtures\LoadMenuData',
        ));
    }

    public function testAction()
    {
        $client = static::createClient(array('root_config' => __DIR__.'/../config/config.php'));
        $crawler = $client->request('GET', '/public/example');

        $this->assertEquals(1, $crawler->filter('#site_menu li a[href="/public/example"]:contains("Login")')->count());
        $this->assertEquals(1, $crawler->filter('#site_menu li a[href="/member?example=1"]:contains("Member")')->count());
        $this->assertEquals(1, $crawler->filter('#site_menu li a[href="http://societo.org/"]:contains("Societo")')->count());
    }
}
