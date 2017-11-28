<?php

namespace Jad\Tests;

use Jad\Serializers\EntitySerializer;
use Jad\Map\AnnotationsMapper;

class EntitySerializerTest extends TestCase
{
    public function testGetIncludedResources()
    {
        $mapper = $this->getMockBuilder('Jad\Map\AnnotationsMapper')
            ->disableOriginalConstructor()
            ->setMethods(['test'])
            ->getMock();

        $request = $this->getMockBuilder('Jad\Request\JsonApiRequest')
            ->disableOriginalConstructor()
            ->setMethods(['test'])
            ->getMock();

        $type = 'test';

        $serializer = new EntitySerializer($mapper, $type, $request);

        $this->assertEquals([], $serializer->getIncludedResources($type, [null]));
    }
}