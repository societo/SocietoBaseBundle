<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\PageGadget;

use Societo\BaseBundle\Test\WebTestCase;

class MemberImageTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(array(
            'Societo\BaseBundle\Tests\Fixtures\LoadAccountData',
            'Societo\BaseBundle\Tests\Fixtures\LoadMemberImageGadgetData',
        ));
    }

    public function testAction()
    {
        $client = static::createClient(array('root_config' => __DIR__.'/../config/config.php'));
        $account = $client->getContainer()->get('doctrine.orm.entity_manager')->find('SocietoAuthenticationBundle:Account', 1);
        $this->login($client, $account);

        $crawler = $client->request('GET', '/gadget_test/1');
        $this->assertEquals(1, $crawler->filter('.gadget ul li')->count());
        $this->assertEquals(1, $crawler->filter('.gadget ul li:first-child a[href="/member/1"]')->count());

        $crawler = $client->request('GET', '/gadget_test/2');
        $this->assertEquals(1, $crawler->filter('.gadget ul li')->count());
        $this->assertEquals(1, $crawler->filter('.gadget ul li:first-child a[href="/member/1"]')->count());

        $crawler = $client->request('GET', '/gadget_test/3');
        $this->assertEquals(2, $crawler->filter('.gadget ul li')->count());
        $this->assertEquals(1, $crawler->filter('.gadget ul li a[href="/member/1"]')->count());

        $crawler = $client->request('GET', '/gadget_test/4');
        $this->assertEquals(2, $crawler->filter('.gadget ul li')->count());
        $this->assertEquals(2, $crawler->filter('.gadget ul li a[href="/member/1"]')->count());
    }
}
