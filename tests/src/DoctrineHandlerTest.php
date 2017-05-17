<?php

namespace Jad\Tests;

use Jad\DoctrineHandler;
use Jad\RequestHandler;
use Jad\Map\ArrayMapper;
use Tobscure\JsonApi\Document;

class DoctrineHandlerTest extends TestCase
{
    public function testGetEntityById()
    {
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
            ->setMethods(['getFieldNames', 'getIdentifier'])
            ->disableOriginalConstructor()
            ->getMock();

        $classMeta
            ->expects($this->any())
            ->method('getFieldNames')
            ->willReturn(['id', 'name']);

        $classMeta
            ->expects($this->any())
            ->method('getIdentifier')
            ->willReturn(['id']);

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

        $mapper = new ArrayMapper($em);
        $mapper->add('article', [
            'entityClass' => 'TestClass'
        ]);

        $_SERVER = ['REQUEST_URI' => '/article'];
        $dh = new DoctrineHandler($mapper, new RequestHandler());

        $resource = $dh->getEntityById(1);

        $document = new Document($resource);

        $expected = '{"data":{"type":"article","id":"1","attributes":{"name":"article1"}}}';
        $this->assertEquals($expected, json_encode($document));
    }

    public function testGetEntities()
    {
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['findBy'])
            ->getMock();

        $entities = [];
        $entities[] = $this->getArticleEntity(['id' => 1, 'name' => 'article1']);
        $entities[] = $this->getArticleEntity(['id' => 2, 'name' => 'article2']);
        $entities[] = $this->getArticleEntity(['id' => 3, 'name' => 'article3']);

        $repo
            ->expects($this->at(0))
            ->method('findBy')
            ->with([], null, null, null)
            ->willReturn($entities);

        $repo
            ->expects($this->at(1))
            ->method('findBy')
            ->with([], ['id' => 'desc'], null, null)
            ->willReturn($entities);

        $repo
            ->expects($this->at(2))
            ->method('findBy')
            ->with([], ['id' => 'asc', 'name' => 'desc'], null, null)
            ->willReturn($entities);

        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->setMethods(['getFieldNames', 'getIdentifier'])
            ->disableOriginalConstructor()
            ->getMock();

        $classMeta
            ->expects($this->any())
            ->method('getFieldNames')
            ->willReturn(['id', 'name']);

        $classMeta
            ->expects($this->any())
            ->method('getIdentifier')
            ->willReturn(['id']);

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

        $mapper = new ArrayMapper($em);
        $mapper->add('article', [
            'entityClass' => 'TestClass'
        ]);

        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $collection = $dh->getEntities();
        $document = new Document($collection);

        $expected = '{"data":[{"type":"article","id":"1","attributes":{"name":"article1"}},{"type":"article","id":"2","attributes":{"name":"article2"}},{"type":"article","id":"3","attributes":{"name":"article3"}}]}';
        $this->assertEquals($expected, json_encode($document));

        $_GET = [
            'sort' => '-id'
        ];

        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $collection = $dh->getEntities();

        $_GET = [
            'sort' => 'id,-name'
        ];

        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $collection = $dh->getEntities();
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