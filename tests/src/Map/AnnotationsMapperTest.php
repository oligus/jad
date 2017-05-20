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
        $this->assertTrue($mapper->hasMapItem('tracks'));
        $this->assertFalse($mapper->hasMapItem('moo'));
        $this->assertTrue($mapper->hasMapItem('albums'));
        $this->assertTrue($mapper->hasMapItem('artists'));
    }
}