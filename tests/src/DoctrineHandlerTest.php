<?php

use Jad\DoctrineHandler;
use Tobscure\JsonApi\Document;

class DoctrineHandlerTest extends TestCase
{
    public function testGetEntityById()
    {
        $mapItem = new Jad\Map\EntityMapItem('article', [
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
            ->willReturn($this->getArticleEntity());

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
            ->expects($this->at(0))
            ->method('getRepository')
            ->with('TestClass')
            ->willReturn($repo);

        $em
            ->expects($this->at(1))
            ->method('getClassMetadata')
            ->with('TestClass')
            ->willReturn($classMeta);

        $dh = new DoctrineHandler($mapItem, $em, new \Jad\RequestHandler());

        $resource = $dh->getEntityById(1);

        $document = new Document($resource);

        $expected = '{"data":{"type":"article","id":"1","attributes":{"name":"Article Name"}}}';
        $this->assertEquals($expected, json_encode($document));
    }

    private function getArticleEntity()
    {
        $entity = $this->getMockBuilder('ArticleEntity')
            ->setMethods(['getId', 'getName'])
            ->getMock();

        $entity->expects($this->any())
            ->method('getId')
            ->willReturn(1);

        $entity->expects($this->any())
            ->method('getName')
            ->willReturn('Article Name');

        return $entity;
    }
}