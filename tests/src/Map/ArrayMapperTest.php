<?php

namespace Jad\Tests\Map;

use Jad\Tests\TestCase;
use Jad\Map\ArrayMapper;
use Jad\Map\MapItem;

class ArrayMapperTest extends TestCase
{
    public function testAdd()
    {
        $map = new ArrayMapper($this->getEm());
        $map->add('test', 'TestClass');
        $this->assertInstanceOf('Jad\Map\MapItem', $map->getMapItem('test'));
    }

    public function testItemExists()
    {
        $mapItem = new MapItem('test', ['entityClass' => 'TestClass']);
        $method = $this->getMethod('Jad\Map\ArrayMapper', 'itemExists');
        $map = new ArrayMapper($this->getEm());
        $this->assertFalse($method->invokeArgs($map, [$mapItem]));
        $map->add('test', 'TestClass');
        $this->assertTrue($method->invokeArgs($map, [$mapItem]));
    }

    public function testHasMapItem()
    {
        $map = new ArrayMapper($this->getEm());
        $map->add('test', 'TestClass');
        $map->add('test2', 'Path\To\TestClass');

        $this->assertTrue($map->hasMapItem('test'));
        $this->assertFalse($map->hasMapItem('moo'));
    }

    private function getEm()
    {
        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getClassMetadata'])
            ->getMock();

        $em
            ->expects($this->any())
            ->method('getClassMetadata')
            ->willReturn($classMeta);

        return $em;
    }
}