<?php

namespace Jad\Tests;

use Jad\DoctrineHandler;
use Jad\RequestHandler;
use Jad\Map\ArrayMapper;
use Jad\Database\Manager;
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

    public function testSetEntityAttribute()
    {
        $mapper = new ArrayMapper($this->getEm());
        $mapper->add('article', ['entityClass' => 'ArticleEntity']);
        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $method = $this->getMethod('Jad\DoctrineHandler', 'setEntityAttribute');

        $article = $this->getArticleEntity(['id' => 2, 'name' => 'test']);

        $article->expects($this->at(0))
            ->method('setName')
            ->with('My new test name');

        $method->invokeArgs($dh, [$article, 'name', 'My new test name']);
    }

    public function testDeleteEntity()
    {
        $_SERVER = ['REQUEST_URI' => '/article'];

        $em = $this->getEm();

        $em
            ->expects($this->at(2))
            ->method('remove')
            ->with($this->getArticleEntity(['id' => 44, 'name' => 'test']));

        $mapper = new ArrayMapper($em);
        $mapper->add('article', ['entityClass' => 'ArticleEntity']);
        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $dh->deleteEntity(44);
    }

    public function testGetEntity()
    {
        $mapper = new ArrayMapper(Manager::getInstance()->getEm());
        $mapper->add('playlists', ['entityClass' => 'Jad\Database\Entities\Playlists']);
        $mapper->add('tracks', ['entityClass' => 'Jad\Database\Entities\Tracks']);

        $dh = new DoctrineHandler($mapper, new RequestHandler());
        $entity = $dh->getEntity('tracks', 44);
        $this->assertInstanceOf('Jad\Database\Entities\Tracks', $entity);
    }

    private function getArticleEntity($params)
    {
        $entity = $this->getMockBuilder('ArticleEntity')
            ->setMethods(['getId', 'getName', 'setName'])
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
        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $entities = [];
        $entities[] = $this->getArticleEntity(['id' => 1, 'name' => 'article1']);
        $entities[] = $this->getArticleEntity(['id' => 2, 'name' => 'article2']);
        $entities[] = $this->getArticleEntity(['id' => 3, 'name' => 'article3']);

        $repo
            ->expects($this->any())
            ->method('find')
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
            ->setMethods(['getRepository', 'getClassMetadata', 'remove', 'flush'])
            ->getMock();

        $em
            ->expects($this->any())
            ->method('getRepository')
            ->with('ArticleEntity')
            ->willReturn($repo);

        $em
            ->expects($this->any())
            ->method('getClassMetadata')
            ->with('ArticleEntity')
            ->willReturn($classMeta);

        return $em;
    }

}