<?php

namespace Jad\Tests;

use Jad\Jad;
use Jad\Map\EntityMap;

require_once 'Mocks.php';

class JadTest extends TestCase
{
    public function testSingle()
    {
        $_SERVER = [
            'REQUEST_URI' => '/api/jad/articles/1',
            'SERVER_NAME' => 'api.markviss.dev',
            'SERVER_PORT' => '80',
            'SERVER_ADDR' => '192.168.56.101',
        ];

        $_GET = [
            'include' => 'author',
            'fields' => [
                'articles' => 'title,body,author'
            ]
        ];

        $articleEntity = Mocks::getInstance()->getArticleEntity();
        $repo = $this->getRepo($articleEntity);
        $classMeta = $this->getClassMeta();

        $em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->setMethods(['getRepository', 'getClassMetadata'])
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

        $entityMap = new EntityMap([
            'articles' => [
                'entityClass' => 'ArticleEntity',
                'id' => 'id'
            ]
        ]);

        $jad = new Jad($em, $entityMap);
        $jad->setPathPrefix('/api/jad');

        $expected = '{"links":{"self":"http:\/\/api.markviss.dev\/api\/jad\/articles\/1"},"data":{"type":"articles","id":"5","attributes":{"title":"Article Title","body":"Article Body","author":"author Entity"}}}';
        $this->assertEquals($expected, $jad->jsonApiResult());
    }

    public function testCollection()
    {
        $_SERVER = ['REQUEST_URI' => '/api/jad/articles'];

        $_GET = [
            'include' => 'author',
            'fields' => [
                'articles' => 'title,body,author'
            ]
        ];

        $this->assertTrue(true);
    }

    public function getRepo(ArticleEntity $articleEntity)
    {

        $repo = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->setMethods(['find'])
            ->getMock();

        $repo
            ->expects($this->any())
            ->method('find')
            ->with(1)
            ->willReturn($articleEntity);

        return $repo;
    }

    public function getClassMeta()
    {
        $classMeta = $this->getMockBuilder('Doctrine\ORM\Mapping\ClassMetadata')
            ->setMethods(['getFieldNames'])
            ->disableOriginalConstructor()
            ->getMock();

        $classMeta
            ->expects($this->any())
            ->method('getFieldNames')
            ->willReturn(['id', 'name', 'title', 'body', 'author', 'unwanted']);

        return $classMeta;
    }
}