<?php

namespace Jad\Tests\Map;

use Jad\Tests\TestCase;
use Jad\Map\EntityMapItem;

class EntityMapItemTest extends TestCase
{
    public function testConstruct()
    {
        $mapItem = new EntityMapItem('test', 'TestClass');
        $this->assertEquals('test', $mapItem->getType());
        $this->assertEquals('TestClass', $mapItem->getEntityClass());

        $mapItem = new EntityMapItem('test2', [
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
}
