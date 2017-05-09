<?php

namespace Jad\Tests;

use Jad\Tests\ArticleEntity;

class Mocks
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

    public function getArticleEntity()
    {
        return new ArticleEntity();
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