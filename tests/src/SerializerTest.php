<?php

namespace Jad\Tests;

use Jad\Serializer;
use Jad\Map\EntityMapItem;

require_once 'Mocks.php';

class SerializerTest extends TestCase
{
    public function testGetId()
    {
        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $mapItem = new EntityMapItem('articles', [
            'classMeta' => $classMeta
        ]);

        $serializer = new Serializer($mapItem);
        $entity = Mocks::getInstance()->getArticleEntity();

        $this->assertEquals(5, $serializer->getId($entity));
    }

    public function testGetAttributes()
    {
        $entity = $this->getMockBuilder('ArticleEntity')
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

        $mapItem = new EntityMapItem('articles', [
            'classMeta' => $classMeta
        ]);

        $serializer = new Serializer($mapItem);

        $result = [
            'roleId' => "Master",
            'name' => "Joe",
            'date' => "2017-05-05 22:36:42",
        ];

        $this->assertEquals($result, $serializer->getAttributes($entity));
    }

    public function testGetPropertyValue()
    {
        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $mapItem = new EntityMapItem('articles', [
            'classMeta' => $classMeta
        ]);

        $serializer = new Serializer($mapItem);

        $articleEntity = Mocks::getInstance()->getArticleEntity();

        $method = $this->getMethod('Jad\Serializer', 'getPropertyValue');
        $this->assertEquals(5, $method->invokeArgs($serializer, [$articleEntity, 'id']));

        $articleEntity->setId(654);

        $this->assertEquals(654, $method->invokeArgs($serializer, [$articleEntity, 'id']));
    }

    public function testNormalizeValue()
    {
        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->getMock();

        $mapItem = new EntityMapItem('articles', [
            'classMeta' => $classMeta
        ]);

        $serializer = new Serializer($mapItem);
        $method = $this->getMethod('Jad\Serializer', 'normalizeValue');
        $this->assertEquals('moo', $method->invokeArgs($serializer, ['moo']));
        $this->assertEquals('2017-05-05 22:36:42', $method->invokeArgs($serializer, [new \DateTime('2017-05-05 22:36:42')]));
    }
}