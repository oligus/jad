<?php

namespace Jad\Tests\Map;

use Jad\Tests\TestCase;
use Jad\Map\MapItem;

/**
 * Class MapItemTest
 * @package Jad\Tests\Map
 */
class MapItemTest extends TestCase
{
    /**
     * @throws \Jad\Exceptions\JadException
     */
    public function testConstruct()
    {
        $mapItem = new MapItem('test', 'TestClass');
        $this->assertEquals('test', $mapItem->getType());
        $this->assertEquals('TestClass', $mapItem->getEntityClass());

        $mapItem = new MapItem('test2', [
            'entityClass' => 'TestClass2'
        ]);

        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->setMethods(['getIdentifier'])
            ->disableOriginalConstructor()
            ->getMock();

        $classMeta
            ->expects($this->any())
            ->method('getIdentifier')
            ->willReturn(['id2']);

        $mapItem->setClassMeta($classMeta);
        $this->assertEquals('test2', $mapItem->getType());
        $this->assertEquals('TestClass2', $mapItem->getEntityClass());
        $this->assertEquals('id2', $mapItem->getIdField());
    }

    public function testIsReadOnly()
    {
        $mapItem = new MapItem('media-types', 'Jad\Database\Entities\MediaTypes');
        $this->assertTrue($mapItem->isReadOnly());

        $mapItem = new MapItem('media-types', 'Jad\Database\Entities\Tracks');
        $this->assertFalse($mapItem->isReadOnly());
    }
}
