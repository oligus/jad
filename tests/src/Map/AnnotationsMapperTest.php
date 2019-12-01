<?php

namespace Jad\Tests\Map;

use Jad\Exceptions\ResourceNotFoundException;
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
        $this->assertTrue($mapper->hasMapItem('test-alias'));
        $this->assertTrue($mapper->hasMapItem('mee'));
    }

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     * @throws \Jad\Exceptions\ResourceNotFoundException
     */
    public function testGetMapItemException()
    {
        $this->expectException(ResourceNotFoundException::class);
        $this->expectExceptionCode(404);
        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $mapper->getMapItem('moo');
    }
}
