<?php

use Jad\EntitySerializer;
use Doctrine\ORM\Mapping\ClassMetadata;

class EntitySerializerTest extends TestCase
{
    public function testGetId()
    {
        $entity = $this->getMockBuilder('EntityMock')
            ->setMethods(['getId'])
            ->getMock();

        $entity
            ->expects($this->at(0))
            ->method('getId')
            ->willReturn(3);

        $serializer = new EntitySerializer('entity');
        $this->assertEquals(3, $serializer->getId($entity));
    }

    public function testGetAttributes()
    {
        $entity = $this->getMockBuilder('MockEntity')
            ->setMethods(['getId', 'getRoleId', 'getName', 'getDate'])
            ->getMock();

        $date = new \DateTime('2017-05-05 22:36:42');

        $entity->expects($this->any())->method('getId')->willReturn(45);
        $entity->expects($this->any())->method('getRoleId')->willReturn('Master');
        $entity->expects($this->any())->method('getName')->willReturn('Joe');
        $entity->expects($this->any())->method('getDate')->willReturn($date);

        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->setMethods(['getFieldNames'])
            ->getMock();

        $classMeta
            ->expects($this->any())
            ->method('getFieldNames')
            ->willReturn(['id', 'roleId', 'name', 'date']);

        $serializer = new EntitySerializer('entity');
        $serializer->setClassMeta($classMeta);

        $result = [
            'roleId' => "Master",
            'name' => "Joe",
            'date' => "2017-05-05 22:36:42",
        ];

        $this->assertEquals($result, $serializer->getAttributes($entity));
    }

    public function testGetPropertyValue()
    {
        $serializer = new EntitySerializer('entity');

        $entity = $this->getMockBuilder('EntityMock')
            ->setMethods(['getId'])
            ->getMock();

        $entity
            ->expects($this->at(0))
            ->method('getId')
            ->willReturn(345);

        $method = $this->getMethod('Jad\EntitySerializer', 'getPropertyValue');
        $this->assertEquals(345, $method->invokeArgs($serializer, [$entity, 'id']));

        $entity = $this->getMockBuilder('EntityMock')
            ->getMock();

        $entity->id = 654;
        $this->assertEquals(654, $method->invokeArgs($serializer, [$entity, 'id']));
    }

    public function testNormalizeValue()
    {
        $serializer = new EntitySerializer('entity');
        $method = $this->getMethod('Jad\EntitySerializer', 'normalizeValue');
        $this->assertEquals('moo', $method->invokeArgs($serializer, ['moo']));
        $this->assertEquals('2017-05-05 22:36:42', $method->invokeArgs($serializer, [new \DateTime('2017-05-05 22:36:42')]));
    }
}