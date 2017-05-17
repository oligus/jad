<?php

namespace Jad\Tests;

use Jad\Serializers\EntitySerializer;
use Jad\Map\ArrayMapper;

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

    public function xtestGetRelationship()
    {
        $relationEntity = $this->getMockBuilder('RelationEntity')
            ->setMethods(['getRelations'])
            ->getMock();

        $entity = $this->getMockBuilder('TestEntity')
            ->setMethods(['getRelation'])
            ->getMock();

        $entity
            ->expects($this->any())
            ->method('getRelation')
            ->willReturn($relationEntity);

        $mapper = new ArrayMapper($this->getEm());
        $mapper->add('test', 'TestEntity');
        $mapper->add('relation', 'RelationEntity');

        $serializer = new EntitySerializer($mapper, 'test');
        $serializer->getRelationship($entity, 'relation');
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