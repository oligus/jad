<?php

use Jad\Map\EntityMap;
use Jad\Map\EntityMapItem;

class EntityMapTest extends TestCase
{

    public function testConstruct()
    {
        $map = new EntityMap([
            'test' => 'TestClass',
            'awesome' => [
                'entityClass' => 'AwesomeClass',
                'idField' => 'myAwesomeId'
            ]
        ]);

        $entityMap = $map->getMap();
        $this->assertEquals(2, count($entityMap));

        /** @var EntityMapItem $item */
        $item = $entityMap[0];
        $this->assertEquals('test', $item->getType());
        $this->assertEquals('TestClass', $item->getEntityClass());
        $this->assertEquals('id', $item->getIdField());

        $item = $entityMap[1];
        $this->assertEquals('awesome', $item->getType());
        $this->assertEquals('AwesomeClass', $item->getEntityClass());
        $this->assertEquals('myAwesomeId', $item->getIdField());
    }

    public function testAdd()
    {
        $map = new EntityMap();
        $map->add('test', 'sfsdf/sfsdf/entity');
        $map->add('test', 'sfsdf/sfsdf/entity');
        $map->add('test3', 'sfsdf/sfsdf/entity');
        $map->add('test4', [
            'entityClass' => 'TestClass',
            'idField' => 'myId'
        ]);

        $entityMap = $map->getMap();

        /** @var EntityMapItem $item */
        $item = $entityMap[0];
        $this->assertEquals('test', $item->getType());

        $item = $entityMap[1];
        $this->assertEquals('test3', $item->getType());
        $this->assertEquals(3, count($entityMap));

        $item = $entityMap[2];
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

        $method = $this->getMethod('Jad\Map\EntityMap', 'itemExists');

        $map = new EntityMap();
        $this->assertFalse($method->invokeArgs($map, [$mapItem]));
        $map->add('test', 'TestClass');
        $this->assertTrue($method->invokeArgs($map, [$mapItem]));
    }

    public function testGetEntityMapItem()
    {
        $map = new EntityMap();
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