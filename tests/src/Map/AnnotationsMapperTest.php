<?php

namespace Jad\Tests\Map;

use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;

class AnnotationsMapperTest extends TestCase
{
    public function testConstruct()
    {
        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $this->assertTrue($mapper->hasMapItem('track'));
        $this->assertFalse($mapper->hasMapItem('moo'));
        $this->assertTrue($mapper->hasMapItem('album'));
        $this->assertTrue($mapper->hasMapItem('artist'));
    }

    /**
     * @expectedException     \Jad\Exceptions\ResourceNotFoundException
     * @expectedExceptionCode 404
     */
    public function testGetMapItemException()
    {
        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $mapper->getMapItem('moo');
    }
}