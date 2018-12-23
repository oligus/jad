<?php

namespace Jad\Tests;

use Jad\Request\JsonApiRequest;
use Jad\Request\Parameters;
use Symfony\Component\HttpFoundation\Request;

class JsonApiRequestTest extends TestCase
{

    private function createRequest()
    {
        $request = Request::createFromGlobals();
        $parameters = new Parameters();
        $parameters->setArguments($request->query->all());
        return new JsonApiRequest($request, $parameters);
    }

    public function testParameters()
    {
        $_GET = [
            'include' => 'author',
            'fields' => [
                'articles' => 'title,body,author'
            ]
        ];

        $_SERVER['REQUEST_URI']  = '/api/jad/articles';

        $request = $this->createRequest();

        $this->assertInstanceOf('Jad\Request\Parameters', $request->getParameters());
        $this->assertEquals(['articles' => ['title', 'body', 'author']], $request->getParameters()->getFields());
        $this->assertTrue(true);
    }

    public function testGetItems()
    {
        $_SERVER['REQUEST_URI']  = '/api/jad/posts/2/moo';
        $request = $this->createRequest();
        $request->setPathPrefix('api/jad');
        $this->assertEquals($request->getItems(), ['posts', '2', 'moo']);

        $_SERVER['REQUEST_URI']  = '/api/v1/accounts/1';
        $request = $this->createRequest();
        $request->setPathPrefix('/api/v1/jad');
        $this->assertEquals($request->getItems(), ['api', 'v1', 'accounts', '1']);
    }

    /**
     * @throws \Jad\Exceptions\RequestException
     */
    public function testGetType()
    {
        // GET Types
        // /articles                            => get all
        // /articles/1                          => get id
        // /articles/1/authors                  => get author relationships
        // /articles/1/relationships/authors    => same as above
        // /articles/1/relationships/comments   => get comments relationships

        $_SERVER['REQUEST_URI']  = '/api/jad/articles';
        $request = $this->createRequest();
        $request->setPathPrefix('api/jad');

        $this->assertEquals($request->getResourceType(), 'articles');
        $this->assertEquals($request->getId(), null);
        $this->assertEquals($request->getRelationship(), []);

        $_SERVER['REQUEST_URI']  = '/api/jad/articles/1';
        $request = $this->createRequest();
        $request->setPathPrefix('api/jad');

        $this->assertEquals($request->getResourceType(), 'articles');
        $this->assertEquals($request->getId(), 1);
        $this->assertEquals($request->getRelationship(), []);

        $_SERVER['REQUEST_URI']  = '/api/jad/articles/1/author';
        $request = $this->createRequest();
        $request->setPathPrefix('api/jad');

        $this->assertEquals($request->getResourceType(), 'articles');
        $this->assertEquals($request->getId(), 1);
        $this->assertEquals($request->getRelationship(), ['view' => 'full', 'type' => 'author']);

        $_SERVER['REQUEST_URI']  = '/api/jad/articles/1/relationships/authors';
        $request = $this->createRequest();
        $request->setPathPrefix('api/jad');

        $this->assertEquals($request->getResourceType(), 'articles');
        $this->assertEquals($request->getId(), 1);
        $this->assertEquals($request->getRelationship(), ['view' => 'list', 'type' => 'authors']);
    }

    /**
     * @expectedException \Jad\Exceptions\RequestException
     * @expectedExceptionMessage Relationship resource type missing
     * @throws \Jad\Exceptions\RequestException
     */
    public function testGetTypeException()
    {
        $_SERVER['REQUEST_URI']  = '/api/jad/articles/1/relationships';
        $request = $this->createRequest();
        $request->setPathPrefix('api/jad');

        $this->assertEquals($request->getResourceType(), 'articles');
        $this->assertEquals($request->getId(), 1);
        $request->getRelationship(); // Exception
    }

    public function testIsCollection()
    {
        $_SERVER['REQUEST_URI']  = '/api/jad/articles/1';
        $request = $this->createRequest();
        $request->setPathPrefix('api/jad');

        $this->assertFalse($request->isCollection());
    }

}