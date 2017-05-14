<?php

namespace Jad\Tests\Map;

use Jad\Tests\TestCase;
use Jad\Map\ArrayMap;
use Jad\Map\EntityMapItem;

class ArrayMapTest extends TestCase
{

    public function testConstruct()
    {
        $map = new ArrayMap([
            'test' => 'TestClass',
            'awesome' => [
                'entityClass' => 'AwesomeClass'
            ]
        ]);

        $entityMap = $map->getMap();
        $this->assertEquals(2, count($entityMap));

        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->setMethods(['getIdentifier'])
            ->disableOriginalConstructor()
            ->getMock();

        $classMeta
            ->expects($this->at(0))
            ->method('getIdentifier')
            ->willReturn(['id']);

        $classMeta
            ->expects($this->at(1))
            ->method('getIdentifier')
            ->willReturn(['myAwesomeId']);

        /** @var EntityMapItem $item */
        $item = $entityMap[0];
        $item->setClassMeta($classMeta);
        $this->assertEquals('test', $item->getType());
        $this->assertEquals('TestClass', $item->getEntityClass());
        $this->assertEquals('id', $item->getIdField());

        $item = $entityMap[1];
        $item->setClassMeta($classMeta);
        $this->assertEquals('awesome', $item->getType());
        $this->assertEquals('AwesomeClass', $item->getEntityClass());
        $this->assertEquals('myAwesomeId', $item->getIdField());
    }

    public function testAdd()
    {
        $map = new ArrayMap();
        $map->add('test', 'sfsdf/sfsdf/entity');
        $map->add('test', 'sfsdf/sfsdf/entity');
        $map->add('test3', 'sfsdf/sfsdf/entity');
        $map->add('test4', [
            'entityClass' => 'TestClass',
            'idField' => 'myId'
        ]);

        $entityMap = $map->getMap();

        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->setMethods(['getIdentifier'])
            ->disableOriginalConstructor()
            ->getMock();

        $classMeta
            ->expects($this->at(0))
            ->method('getIdentifier')
            ->willReturn(['myId']);

        /** @var EntityMapItem $item */
        $item = $entityMap[0];
        $this->assertEquals('test', $item->getType());

        $item = $entityMap[1];
        $this->assertEquals('test3', $item->getType());
        $this->assertEquals(3, count($entityMap));

        $item = $entityMap[2];
        $item->setClassMeta($classMeta);
        $this->assertEquals('test4', $item->getType());
        $this->assertEquals('TestClass', $item->getEntityClass());
        $this->assertEquals('myId', $item->getIdField());
    }

    public function testItemExists()
    {
        $mapItem = new EntityMapItem('test', [
            'entityClass' => 'TestClass',
            'idField' => 'id'
        ]);

        $method = $this->getMethod('Jad\Map\ArrayMap', 'itemExists');

        $map = new ArrayMap();
        $this->assertFalse($method->invokeArgs($map, [$mapItem]));
        $map->add('test', 'TestClass');
        $this->assertTrue($method->invokeArgs($map, [$mapItem]));
    }

    public function testGetEntityMapItem()
    {
        $map = new ArrayMap();
        $map->add('test', 'TestClass');
        $map->add('test2', 'Path\To\TestClass');
        $map->add('test3', [
            'entityClass' => 'MyTestClass',
            'idField' => 'myId'
        ]);

        $this->assertEquals(new EntityMapItem('test', 'TestClass'), $map->getEntityMapItem('test'));
        $this->assertEquals(new EntityMapItem('test2', 'Path\To\TestClass'), $map->getEntityMapItem('test2'));
        $this->assertEquals(new EntityMapItem('test3', [
            'entityClass' => 'MyTestClass',
            'idField' => 'myId'
        ]), $map->getEntityMapItem('test3'));
        $this->assertEquals(new EntityMapItem('mytest', 'Mytest'), $map->getEntityMapItem('mytest'));

    }
}