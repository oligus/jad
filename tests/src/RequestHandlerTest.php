<?php

use Jad\RequestHandler;

class RequestHandlerTest extends TestCase
{
    public function testParameters()
    {
        $_GET = [
            'include' => 'author',
            'fields' => [
                'articles' => 'title,body,author'
            ]
        ];

        $_SERVER = [
            'REQUEST_URI' => '/api/jad/articles'
        ];

        $rh = new RequestHandler();

        $this->assertInstanceOf('Tobscure\JsonApi\Parameters', $rh->getParameters());
        $this->assertEquals(['articles' => ['title', 'body', 'author']], $rh->getParameters()->getFields());
        $this->assertTrue(true);
    }

    public function testGetItems()
    {
        $_SERVER = ['REQUEST_URI' => '/api/jad/posts/2/moo'];
        $rh = new RequestHandler();
        $rh->setPathPrefix('api/jad');
        $this->assertEquals($rh->getItems(), ['posts', '2', 'moo']);

        $_SERVER = ['REQUEST_URI' => '/api/v1/accounts/1'];
        $rh = new RequestHandler();
        $rh->setPathPrefix('/api/v1/jad');
        $this->assertEquals($rh->getItems(), ['api', 'v1', 'accounts', '1']);
    }

    public function testGetType()
    {
        // GET Types
        // /articles                            => get all
        // /articles/1                          => get id
        // /articles/1/author                   => get author relationships
        // /articles/1/relationships/author     => same as above
        // /articles/1/relationships/comments   => get comments relationships

        $_SERVER = ['REQUEST_URI' => '/api/jad/articles'];
        $rh = new RequestHandler();
        $rh->setPathPrefix('api/jad');

        $this->assertEquals($rh->getType(), 'articles');
        $this->assertEquals($rh->getId(), null);
        $this->assertEquals($rh->getRelationship(), null);

        $_SERVER = ['REQUEST_URI' => '/api/jad/articles/1'];
        $rh = new RequestHandler();
        $rh->setPathPrefix('api/jad');

        $this->assertEquals($rh->getType(), 'articles');
        $this->assertEquals($rh->getId(), 1);
        $this->assertEquals($rh->getRelationship(), null);

        $_SERVER = ['REQUEST_URI' => '/api/jad/articles/1/author'];
        $rh = new RequestHandler();
        $rh->setPathPrefix('api/jad');

        $this->assertEquals($rh->getType(), 'articles');
        $this->assertEquals($rh->getId(), 1);
        $this->assertEquals($rh->getRelationship(), 'author');

        $_SERVER = ['REQUEST_URI' => '/api/jad/articles/1/relationship/author'];
        $rh = new RequestHandler();
        $rh->setPathPrefix('api/jad');

        $this->assertEquals($rh->getType(), 'articles');
        $this->assertEquals($rh->getId(), 1);
        $this->assertEquals($rh->getRelationship(), 'author');
    }

    /**
     * @expectedException \Jad\Exceptions\JadException
     * @expectedExceptionMessage Relationship entity missing
     */
    public function testGetTypeException()
    {
        $_SERVER = ['REQUEST_URI' => '/api/jad/articles/1/relationship'];
        $rh = new RequestHandler();
        $rh->setPathPrefix('api/jad');

        $this->assertEquals($rh->getType(), 'articles');
        $this->assertEquals($rh->getId(), 1);
        $rh->getRelationship(); // Exception
    }
}