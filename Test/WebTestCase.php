<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Test;

use Symfony\Component\HttpKernel\Util\Filesystem;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase as BaseWebTestCase;

use Symfony\Bundle\DoctrineFixturesBundle\Common\DataFixtures\Loader;
use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;

use Doctrine\DBAL\Types\Type;

use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\HttpKernel\KernelEvents;

class WebTestCase extends BaseWebTestCase
{
    static public $loaded = false;

    public function setUp()
    {
        $filesystem = new \Symfony\Component\HttpKernel\Util\Filesystem();
        $filesystem->remove(self::createKernel()->getCacheDir());
    }

    static protected function createKernel(array $options = array())
    {
        if (empty($options['root_config'])) {
            return parent::createKernel($options);
        }

        if (!self::$loaded) {
            if (!isset($_SERVER['SYMFONY__KERNEL__ROOT_DIR'])) {
                $_SERVER['SYMFONY__KERNEL__ROOT_DIR'] = static::getPhpUnitXmlDir();
            }

            $class = file_get_contents(__DIR__.'/AppKernel.php.template');
            eval(strtr($class, array(
                '#PARENT_CLASS#' => static::getKernelClass(),
                '#ROOT_DIR#' => $_SERVER['SYMFONY__KERNEL__ROOT_DIR'],
            )));

            self::$loaded = true;
        }

        $class = 'Societo\BaseBundle\Test\TestAppKernel';

        return new $class(
            isset($options['environment']) ? $options['environment'] : 'test',
            isset($options['debug']) ? $options['debug'] : true,
            isset($options['root_config']) ? $options['root_config'] : null
        );
    }

    protected function loadFixtures(array $fixtures = array(), $rebuild = true)
    {
        $kernel = $this->createKernel(array('environment' => 'test'));
        $kernel->boot();

        $container = $kernel->getContainer();

        $em = $container->get('doctrine.orm.entity_manager');
        $conn = $em->getConnection();

        if ($rebuild) {
            $this->rebuildDatabase($em);
        }

        $loader = new Loader($container);
        foreach ($fixtures as $fixture) {
            $loader->addFixture(new $fixture());
        }

        $executor = new ORMExecutor($em);
        $executor->execute($loader->getFixtures(), true);

        $conn->close();
    }

    protected function rebuildDatabase($em)
    {
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->dropDatabase();

        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadatas)) {
            $schemaTool->createSchema($metadatas);
        }
    }

    public function login($client, $account)
    {
        $token = new UsernamePasswordToken($account, null, 'main', $account->getRoles());
        $func = function ($event) use ($token) {
            $event->getRequest()->getSession()->set('_security_main', serialize($token));
        };

        $client->getContainer()->get('event_dispatcher')->addListener(KernelEvents::RESPONSE, $func);
        $client->request('GET', '/logout');
        $client->getContainer()->get('event_dispatcher')->removeListener(KernelEvents::RESPONSE, $func);
    }
}
