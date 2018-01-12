<?php

namespace Jad\Tests;

use Jad\Serializers\IncludedSerializer;
use Jad\Map\AnnotationsMapper;

class IncludedSerializerTest extends TestCase
{
    public function testGetRelationships()
    {
        $mapper = $this->getMockBuilder('Jad\Map\AnnotationsMapper')
            ->disableOriginalConstructor()
            ->setMethods(['test'])
            ->getMock();

        $request = $this->getMockBuilder('Jad\Request\JsonApiRequest')
            ->disableOriginalConstructor()
            ->setMethods(['test'])
            ->getMock();

        $entity = $this->getMockBuilder('Jad\Database\Entities\Albums')->getMock();

        $type = 'test';

        $serializer = new IncludedSerializer($mapper, $type, $request);

        $this->assertEquals([], $serializer->getRelationships($entity));
        $this->assertEquals([], $serializer->getIncluded($type, $entity, []));
    }
}