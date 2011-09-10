<?php

/**
 * This file is applied CC0 <http://creativecommons.org/publicdomain/zero/1.0/>
 */

namespace Societo\BaseBundle\Tests\DBAL;

use Doctrine\DBAL\Types\Type;

class BlobTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $_platform, $_type;

    protected function setUp()
    {
        if (!Type::hasType('blob')) {
            Type::addType('blob', 'Societo\BaseBundle\DBAL\BlobType');
        }

        $this->_platform = new \Doctrine\Tests\DBAL\Mocks\MockPlatform();
        $this->_platform->registerDoctrineTypeMapping('blob', 'blob');

        $this->_type = Type::getType('blob');
    }

    public function testBlobConvertsToDatabaseValue()
    {
        $this->assertInternalType('string', $this->_type->convertToDatabaseValue('BIN', $this->_platform));
    }

    public function testBooleanConvertsToPHPValue()
    {
        $this->assertInternalType('string', $this->_type->convertToPHPValue('BIN', $this->_platform));
    }

    public function testBooleanNullConvertsToPHPValue()
    {
        $this->assertNull($this->_type->convertToPHPValue(null, $this->_platform));
    }
}
