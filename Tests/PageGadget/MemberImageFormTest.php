<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\PageGadget;

use Societo\BaseBundle\Test\WebTestCase;

class MemberImageFormTest extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(array(
            'Societo\BaseBundle\Tests\Fixtures\LoadAccountData',
            'Societo\BaseBundle\Tests\Fixtures\LoadMemberImageFormGadgetData',
        ));
    }

    public function testAction()
    {
        $client = static::createClient(array('root_config' => __DIR__.'/../config/config.php'));
        $account = $client->getContainer()->get('doctrine.orm.entity_manager')->find('SocietoAuthenticationBundle:Account', 1);
        $this->login($client, $account);

        $crawler = $client->request('GET', '/gadget_test/1');
        $crawler = $client->submit($crawler->selectButton('Save')->form());
        $this->assertEquals(1, $crawler->filter('li:contains("This value should not be null")')->count());

        $crawler = $client->request('GET', '/gadget_test/1');
        $form = $crawler->selectButton('Save')->form();
        $form['societo_base_member_image[file]']->upload(__FILE__);
        $crawler = $client->submit($form);
        $this->assertEquals(1, $crawler->filter('li:contains("The mime type of the file is invalid")')->count());

        $crawler = $client->request('GET', '/gadget_test/1');
        $form = $crawler->selectButton('Save')->form();
        $form['societo_base_member_image[file]']->upload(__DIR__.'/../Fixtures/red.gif');
        $crawler = $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}
