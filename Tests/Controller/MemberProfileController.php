<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Controller;

use Societo\BaseBundle\Test\WebTestCase;

class MemberProfileController extends WebTestCase
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
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $account = $em->find('SocietoAuthenticationBundle:Account', 1);
        $this->login($client, $account);

        $client->request('GET', '/member/profile/update/1');
        $this->assertEquals(405, $client->getResponse()->getStatusCode());

        $crawler = $client->request('POST', '/member/profile/update/1');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals(1, $crawler->filter('#societo_base_member_profile')->count());

        $form = $crawler->selectButton('Save')->form(array(
            'societo_base_member_profile[hobby]' => 'Swimming',
            'societo_base_member_profile[nickname]' => 'Kousuke Ebihara',
        ));
        $client->submit($form);
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals('Changes are saved successfully', $client->getContainer()->get('session')->getFlash('success'));

        $crawler = $client->request('GET', '/gadget_test/1');
        $this->assertEquals(1, $crawler->filter('input[type="text"][id="societo_base_member_profile_hobby"][value="Swimming"]')->count());
        $this->assertEquals(1, $crawler->filter('input[type="text"][id="societo_base_member_profile_nickname"][value="Kousuke Ebihara"]')->count());
        $form = $crawler->selectButton('Save')->form(array(
            'societo_base_member_profile[hobby]' => 'Soccer',
        ));
        $client->submit($form);
        $crawler = $client->request('GET', '/gadget_test/1');
        $this->assertEquals(1, $crawler->filter('input[type="text"][id="societo_base_member_profile_hobby"][value="Soccer"]')->count());
        $this->assertEquals(1, $crawler->filter('input[type="text"][id="societo_base_member_profile_nickname"][value="Kousuke Ebihara"]')->count());
    }
}
