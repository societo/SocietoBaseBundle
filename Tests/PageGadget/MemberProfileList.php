<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\PageGadget;

use Societo\BaseBundle\Test\WebTestCase;

class MemberProfileList extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(array(
            'Societo\BaseBundle\Tests\Fixtures\LoadAccountData',
            'Societo\BaseBundle\Tests\Fixtures\LoadMemberProfileListGadgetData',
        ));
    }

    public function testAction()
    {
        $client = static::createClient(array('root_config' => __DIR__.'/../config/config.php'));
        $account = $client->getContainer()->get('doctrine.orm.entity_manager')->find('SocietoAuthenticationBundle:Account', 1);
        $this->login($client, $account);

        $crawler = $client->request('GET', '/member_gadget_test/1/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/Swimming/', $client->getResponse()->getContent());

        $crawler = $client->request('GET', '/member_gadget_test/1/2');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/Eating/', $client->getResponse()->getContent());

        $crawler = $client->request('GET', '/member_gadget_test/1/19880423');
        $this->assertEquals(500, $client->getResponse()->getStatusCode());
    }

    public function testListingProfile()
    {
        $client = static::createClient(array('root_config' => __DIR__.'/../config/config.php'));
        $account = $client->getContainer()->get('doctrine.orm.entity_manager')->find('SocietoAuthenticationBundle:Account', 1);
        $this->login($client, $account);

        $crawler = $client->request('GET', '/member_gadget_test/1/1');
        $this->assertEquals(7, $crawler->filter('.gadget th')->count());
        $this->assertEquals(1, $crawler->filter('.gadget th:contains("hobby") + td:contains("Swimming")')->count());
        $this->assertEquals(2, $crawler->filter('.gadget th:contains("Self Introduction") + td:contains("Hi!") > br')->count());
        $this->assertEquals(1, $crawler->filter('.gadget th:contains("age") + td:contains("23")')->count());
        $this->assertEquals(1, $crawler->filter('.gadget th:contains("gender") + td:contains("Masculine")')->count());
        $this->assertEquals(1, $crawler->filter('.gadget th:contains("wedding_anniversary") + td:contains("2011-08-07")')->count());
        $this->assertEquals(1, $crawler->filter('.gadget th:contains("birthday") + td:contains("1988-04-23")')->count());
        $this->assertEquals(1, $crawler->filter('.gadget th:contains("fun") + td:contains("yes")')->count());
    }
}
