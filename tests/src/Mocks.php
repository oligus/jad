<?php

namespace Jad\Tests;

use Jad\Tests\ArticleEntity;

class Mocks extends TestCase
{
    /**
     * @var Mocks $instance
     */
    public static $instance;

    /**
     * @return Mocks
     */
    public static function getInstance()
    {
        if(!self::$instance instanceof Mocks) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    // 'articles' => 'title,body,author'
    public function getArticleEntity()
    {
        return new ArticleEntity();
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