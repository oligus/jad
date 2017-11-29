<?php

namespace Jad\Tests\Document;

use Jad\Tests\TestCase;
use Jad\Document\Meta;

class MetaTest extends TestCase
{
    public function testIsEmpty()
    {
        $meta = new Meta();
        $this->assertTrue($meta->isEmpty());
        $meta->setPages(2);
        $this->assertFalse($meta->isEmpty());
    }

    public function testJsonSerialize()
    {
        $meta = new Meta();
        $meta->setCount(5);
        $meta->setPages(2);

        $result = json_encode($meta);
        $this->assertEquals('{"count":5,"pages":2}', $result);
    }
}