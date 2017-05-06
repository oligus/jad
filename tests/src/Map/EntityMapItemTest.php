<?php

use Jad\Map\EntityMapItem;

class EntityMapItemTest extends TestCase
{
    public function testConstruct()
    {
        $mapItem = new EntityMapItem('test', 'TestClass');
        $this->assertEquals('test', $mapItem->getType());
        $this->assertEquals('TestClass', $mapItem->getEntityClass());

        $mapItem = new EntityMapItem('test2', [
            'entityClass' => 'TestClass2',
            'idField' => 'id2'
        ]);
        $this->assertEquals('test2', $mapItem->getType());
        $this->assertEquals('TestClass2', $mapItem->getEntityClass());
        $this->assertEquals('id2', $mapItem->getIdField());
    }
}
