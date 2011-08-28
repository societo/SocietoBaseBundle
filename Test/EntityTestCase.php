<?php

/**
 * SocietoBaseBundle
 * Copyright (C) 2011 Kousuke Ebihara
 *
 * This program is under the EPL/GPL/LGPL triple license.
 * Please see the Resources/meta/LICENSE file that was distributed with this file.
 */

namespace Societo\BaseBundle\Test;

use Symfony\Bridge\Doctrine\Annotations\IndexedReader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\ORM\EntityManager;
use Doctrine\DBAL\Types\Type;

class EntityTestCase extends \PHPUnit_Framework_TestCase
{
    protected function rebuildDatabase($em)
    {
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->dropDatabase();

        $metadatas = $em->getMetadataFactory()->getAllMetadata();
        if (!empty($metadatas)) {
            $schemaTool->createSchema($metadatas);
        }
    }

    public function createClassFileDirectoryPaths(array $classes)
    {
        $results = array();

        foreach ($classes as $class) {
            $reflection = new \ReflectionClass($class);
            $results[] = dirname($reflection->getFileName());
        }

        return $results;
    }

    public function createTestEntityManager($entityPaths = array())
    {
        $config = new \Doctrine\ORM\Configuration();
        $config->setAutoGenerateProxyClasses(true);
        $config->setProxyDir(sys_get_temp_dir());
        $config->setProxyNamespace('SocietoTests\Doctrine');
        $config->setMetadataDriverImpl(new AnnotationDriver(new IndexedReader(new AnnotationReader()), $entityPaths));
        $config->setQueryCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        $config->setMetadataCacheImpl(new \Doctrine\Common\Cache\ArrayCache());
        $params = array(
            'driver' => 'pdo_sqlite',
            'memory' => true,
        );

        if (!Type::hasType('blob')) {
            Type::addType('blob', 'Societo\BaseBundle\DBAL\BlobType');
        }

        $em = EntityManager::create($params, $config);
        $em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('blob', 'blob');

        return $em;
    }
}
