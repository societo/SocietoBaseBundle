<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\PageGadget;

use Societo\BaseBundle\Test\WebTestCase;

class EditProfileForm extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(array(
            'Societo\BaseBundle\Tests\Fixtures\LoadAccountData',
            'Societo\BaseBundle\Tests\Fixtures\LoadEditProfileFormGadgetData',
        ));
    }

    public function testAction()
    {
        $client = static::createClient(array('root_config' => __DIR__.'/../config/config.php'));
        $account = $client->getContainer()->get('doctrine.orm.entity_manager')->find('SocietoAuthenticationBundle:Account', 1);
        $this->login($client, $account);

        $crawler = $client->request('GET', '/gadget_test/1');
        $this->assertEquals(1, $crawler->filter('input[type="text"][id="societo_base_member_profile_hobby"]')->count());
        $client->submit($crawler->selectButton('Save')->form());

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertRegExp('/This value should not be blank/', $client->getResponse()->getContent());
    }
}
