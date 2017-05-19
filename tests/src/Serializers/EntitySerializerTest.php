<?php

namespace Jad\Tests;

use Jad\Serializers\EntitySerializer;
use Jad\Map\ArrayMapper;
use Tobscure\JsonApi\Document;
use Tobscure\JsonApi\Resource;

class EntitySerializerTest extends TestCase
{
    public function testGetId()
    {
        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->setMethods(['getIdentifier'])
            ->getMock();

        $classMeta
            ->expects($this->any())
            ->method('getIdentifier')
            ->willReturn(['id']);

        $mapper = new ArrayMapper($this->getEm());
        $mapper->add('articles', [
            'classMeta' => $classMeta
        ]);

        $serializer = new EntitySerializer($mapper, 'articles');
        $entity = $this->getArticleEntity(['id' => 5, 'name' => 'Test']);

        $this->assertEquals(5, $serializer->getId($entity));
    }

    public function testGetItem()
    {
        $mapper = new ArrayMapper($this->getEm());
        $mapper->add('articles', []);
        $serializer = new EntitySerializer($mapper, 'articles');
        $mapItem = $serializer->getMapItem();

        $this->assertInstanceOf('Jad\Map\MapItem', $mapItem);
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

        $mapper = new ArrayMapper($this->getEm());
        $mapper->add('articles', []);

        $serializer = new EntitySerializer($mapper, 'articles');

        $result = [
            'roleId' => "Master",
            'name' => "Joe",
            'date' => "2017-05-05 22:36:42",
        ];

        $this->assertEquals($result, $serializer->getAttributes($entity));
    }

    public function testGetRelationship()
    {
        $relationEntity = $this->getMockBuilder('RelationEntity')
            ->setMethods(['getRelations', 'getId', 'getTest'])
            ->getMock();

        $relationEntity
            ->expects($this->any())
            ->method('getId')
            ->willReturn(55);

        $relationEntity
            ->expects($this->any())
            ->method('getTest')
            ->willReturn('Test Attribute');

        $entity = $this->getMockBuilder('TestEntity')
            ->setMethods(['getRelation', 'getId', 'getRoleId', 'getName', 'getDate'])
            ->getMock();

        $entity
            ->expects($this->any())
            ->method('getId')
            ->willReturn(42);

        $entity
            ->expects($this->any())
            ->method('getRoleId')
            ->willReturn('guest');

        $entity
            ->expects($this->any())
            ->method('getName')
            ->willReturn('John Doe');

        $entity
            ->expects($this->any())
            ->method('getDate')
            ->willReturn('2017-05-18');

        $entity
            ->expects($this->any())
            ->method('getRelation')
            ->willReturn($relationEntity);

        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->setMethods(['getIdentifier', 'getFieldNames', 'hasAssociation'])
            ->getMock();

        $classMeta
            ->expects($this->any())
            ->method('getIdentifier')
            ->willReturn(['id']);

        $classMeta
            ->expects($this->any())
            ->method('getFieldNames')
            ->willReturn(['id', 'roleId', 'name', 'date']);

        $classMeta
            ->expects($this->any())
            ->method('hasAssociation')
            ->willReturn(true);

        $classMetaRelation = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->setMethods(['getIdentifier', 'getFieldNames', 'hasAssociation'])
            ->getMock();

        $classMetaRelation
            ->expects($this->any())
            ->method('getIdentifier')
            ->willReturn(['id']);

        $classMetaRelation
            ->expects($this->any())
            ->method('getFieldNames')
            ->willReturn(['id', 'test']);

        $classMetaRelation
            ->expects($this->any())
            ->method('hasAssociation')
            ->willReturn(true);

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getClassMetadata'])
            ->getMock();

        $em
            ->expects($this->at(0))
            ->method('getClassMetadata')
            ->willReturn($classMeta);

        $em
            ->expects($this->at(1))
            ->method('getClassMetadata')
            ->willReturn($classMetaRelation);

        $mapper = new ArrayMapper($em);
        $mapper->add('test', 'TestEntity');
        $mapper->add('relation', 'RelationEntity');

        $serializer = new EntitySerializer($mapper, 'test');

        $resource = new Resource($entity, $serializer);
        $document = new Document($resource->with('relation'));

        $expected = '{"data":{"type":"test","id":"42","attributes":{"roleId":"guest","name":"John Doe","date":"2017-05-18"},"relationships":{"relation":{"data":{"type":"relation","id":"55"},"links":{"related":"path"}}}},"included":[{"type":"relation","id":"55","attributes":{"test":"Test Attribute"}}]}';
        $this->assertEquals($expected, json_encode($document));
    }

    public function testGetPropertyValue()
    {
        $mapper = new ArrayMapper($this->getEm());
        $mapper->add('articles', []);

        $serializer = new EntitySerializer($mapper, 'articles');

        $articleEntity = $this->getArticleEntity(['id' => 5, 'name' => 'Test']);

        $method = $this->getMethod('Jad\Serializers\EntitySerializer', 'getPropertyValue');
        $this->assertEquals(5, $method->invokeArgs($serializer, [$articleEntity, 'id']));

        $articleEntity = $this->getArticleEntity(['id' => 654, 'name' => 'Test']);

        $this->assertEquals(654, $method->invokeArgs($serializer, [$articleEntity, 'id']));
    }

    public function testNormalizeValue()
    {
        $mapper = new ArrayMapper($this->getEm());
        $mapper->add('articles', []);

        $serializer = new EntitySerializer($mapper, 'articles');

        $method = $this->getMethod('Jad\Serializers\EntitySerializer', 'normalizeValue');
        $this->assertEquals('moo', $method->invokeArgs($serializer, ['moo']));
        $this->assertEquals('2017-05-05 22:36:42', $method->invokeArgs($serializer, [new \DateTime('2017-05-05 22:36:42')]));
    }

    private function getArticleEntity($params)
    {
        $entity = $this->getMockBuilder('ArticleEntity')
            ->setMethods(['getId', 'getName'])
            ->getMock();

        $entity->expects($this->any())
            ->method('getId')
            ->willReturn($params['id']);

        $entity->expects($this->any())
            ->method('getName')
            ->willReturn($params['name']);

        return $entity;
    }

    private function getEm()
    {
        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->disableOriginalConstructor()
            ->setMethods(['getIdentifier', 'getFieldNames', 'hasAssociation'])
            ->getMock();

        $classMeta
            ->expects($this->any())
            ->method('getIdentifier')
            ->willReturn(['id']);

        $classMeta
            ->expects($this->any())
            ->method('getFieldNames')
            ->willReturn(['id', 'roleId', 'name', 'date']);

        $classMeta
            ->expects($this->any())
            ->method('hasAssociation')
            ->willReturn(true);

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