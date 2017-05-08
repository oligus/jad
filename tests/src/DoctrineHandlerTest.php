<?php

namespace Jad\Tests;

use Jad\DoctrineHandler;
use Jad\Map\EntityMapItem;
use Tobscure\JsonApi\Document;

class DoctrineHandlerTest extends TestCase
{
    public function testGetEntityById()
    {
        $mapItem = new EntityMapItem('article', [
            'entityClass' => 'TestClass'
        ]);

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $repo
            ->expects($this->at(0))
            ->method('find')
            ->with(1)
            ->willReturn($this->getArticleEntity(['id' => 1, 'name' => 'article1']));

        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->setMethods(['getFieldNames'])
            ->disableOriginalConstructor()
            ->getMock();

        $classMeta
            ->expects($this->any())
            ->method('getFieldNames')
            ->willReturn(['id', 'name']);

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getRepository', 'getClassMetadata'])
            ->getMock();

        $em
            ->expects($this->any())
            ->method('getRepository')
            ->with('TestClass')
            ->willReturn($repo);

        $em
            ->expects($this->any())
            ->method('getClassMetadata')
            ->with('TestClass')
            ->willReturn($classMeta);

        $dh = new DoctrineHandler($mapItem, $em, new \Jad\RequestHandler());

        $resource = $dh->getEntityById(1);

        $document = new Document($resource);

        $expected = '{"data":{"type":"article","id":"1","attributes":{"name":"article1"}}}';
        $this->assertEquals($expected, json_encode($document));
    }

    public function testGetEntities()
    {
        $mapItem = new EntityMapItem('article', [
            'entityClass' => 'TestClass'
        ]);

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['findBy'])
            ->getMock();

        $entities = [];
        $entities[] = $this->getArticleEntity(['id' => 1, 'name' => 'article1']);
        $entities[] = $this->getArticleEntity(['id' => 2, 'name' => 'article2']);
        $entities[] = $this->getArticleEntity(['id' => 3, 'name' => 'article3']);

        $repo
            ->expects($this->any())
            ->method('findBy')
            ->willReturn($entities);

        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->setMethods(['getFieldNames'])
            ->disableOriginalConstructor()
            ->getMock();

        $classMeta
            ->expects($this->any())
            ->method('getFieldNames')
            ->willReturn(['id', 'name']);

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getRepository', 'getClassMetadata'])
            ->getMock();

        $em
            ->expects($this->any())
            ->method('getRepository')
            ->with('TestClass')
            ->willReturn($repo);

        $em
            ->expects($this->any())
            ->method('getClassMetadata')
            ->with('TestClass')
            ->willReturn($classMeta);

        $dh = new DoctrineHandler($mapItem, $em, new \Jad\RequestHandler());
        $collection = $dh->getEntities();
        $document = new Document($collection);

        $expected = '{"data":[{"type":"article","id":"1","attributes":{"name":"article1"}},{"type":"article","id":"2","attributes":{"name":"article2"}},{"type":"article","id":"3","attributes":{"name":"article3"}}]}';
        $this->assertEquals($expected, json_encode($document));
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

}