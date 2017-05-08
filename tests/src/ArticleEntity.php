<?php

namespace Jad\Tests;

class ArticleEntity
{
    private $id = 5;

    private $name       = 'Article Name';
    private $title      = 'Article Title';
    private $body       = 'Article Body';
    private $author     = 'author Entity';
    private $unwanted   = 'Unwanted property';

    public function __construct()
    {
    }

    public function setId($id)
    {
        $this->id = $id;
    }

}