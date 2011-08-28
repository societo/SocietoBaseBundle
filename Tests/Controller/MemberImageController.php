<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\Controller;

use Societo\BaseBundle\Test\WebTestCase;

class MemberImageController extends WebTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->loadFixtures(array(
            'Societo\BaseBundle\Tests\Fixtures\LoadAccountData',
            'Societo\BaseBundle\Tests\Fixtures\LoadMemberImageFormGadgetData',
        ));
    }

    public function testPostAction()
    {
        $client = static::createClient(array('root_config' => __DIR__.'/../config/config.php'));
        $em = $client->getContainer()->get('doctrine.orm.entity_manager');
        $account = $em->find('SocietoAuthenticationBundle:Account', 1);
        $this->login($client, $account);

        $client->request('GET', '/memberImage/add/1');
        $this->assertEquals(405, $client->getResponse()->getStatusCode());

        $crawler = $client->request('GET', '/gadget_test/1');
        $csrfToken = $crawler->filter('#societo_base_member_image__token')->attr('value');

        $client->request('POST', '/memberImage/add/1',
            array('societo_base_member_image' => array('redirect_to' => '_root', '_token' => $csrfToken)),
            array('societo_base_member_image' => array('file' => __DIR__.'/../Fixtures/red.gif')));

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals('Changes are saved successfully', $client->getContainer()->get('session')->getFlash('success'));

        $this->markTestIncomplete();
/*
        $client->request('GET', '/image/member/1');
        $this->assertEquals(md5(file_get_contents(__DIR__.'/../Fixtures/red.gif')), md5($client->getResponse()->getContent()));

        $client->request('POST', '/memberImage/add/1',
            array('societo_base_member_image' => array('redirect_to' => '_root', '_token' => $csrfToken)),
            array('societo_base_member_image' => array('file' => __DIR__.'/../Fixtures/blue.gif')));

        $client->request('GET', '/image/member/1');
        $this->assertEquals(md5(file_get_contents(__DIR__.'/../Fixtures/blue.gif')), md5($client->getResponse()->getContent()));
        */
    }

    public function testShowAction()
    {
        $this->markTestIncomplete();
    }
}
