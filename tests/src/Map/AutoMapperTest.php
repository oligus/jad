<?php

namespace Jad\Tests\Map;

use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Map\AutoMapper;

class AutoMapperTest extends TestCase
{
    public function testConstruct()
    {
        $mapper = new AutoMapper(Manager::getInstance()->getEm(), ['albums']);
        $this->assertTrue($mapper->hasMapItem('tracks'));
        $this->assertFalse($mapper->hasMapItem('moo'));
        $this->assertFalse($mapper->hasMapItem('albums'));
        $this->assertTrue($mapper->hasMapItem('artists'));
    }
}