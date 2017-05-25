<?php

use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Map\ArrayMapper;
use Jad\Jad;

class DoctrineTest extends TestCase
{
    public function testMoo()
    {
        $_SERVER = [
            'SERVER_NAME' => 'api.test.com',
            'SERVER_PORT' => '80',
            'REQUEST_URI' => '/api/jad/albums/1',
        ];

        $_GET = [];

        $mapper = new ArrayMapper(Manager::getInstance()->getEm());
        $mapper->add('albums', 'Jad\Database\Entities\Albums');
        $mapper->add('artists', 'Jad\Database\Entities\Artists');

        $jad = new Jad($mapper);
        $jad->setPathPrefix('/api/jad');

        $em = Manager::getInstance()->getEm();

        /** @var \Jad\Database\Entities\Albums $album */
        $album = $em->getRepository('Jad\Database\Entities\Albums')->find(1);

        // GET /api?include=author,comments

        $result = json_decode($jad->jsonApiResult());

        //print_r(json_encode($result));
        $this->assertFalse(false);
    }

    public function testSort()
    {
        $_SERVER = [
            'REQUEST_URI' => '/api/jad/tracks',
        ];

        $_GET = [
            'page' => [
                'offset' => 0,
                'limit' => 5
            ],

            'sort' => '-name'
        ];

        $mapper = new ArrayMapper(Manager::getInstance()->getEm());
        $mapper->add('tracks', ['entityClass' => 'Jad\Database\Entities\Tracks']);

        $jad = new Jad($mapper);
        $jad->setPathPrefix('/api/jad');

        $result = json_decode($jad->jsonApiResult());

        $this->assertEquals(5, count($result->data));
        $this->assertEquals('2014', $result->data[2]->id);
        $this->assertEquals('Something In The Way', $result->data[2]->attributes->name);

        $_GET = [
            'page' => [
                'offset' => 0,
                'limit' => 10
            ],

            'sort' => 'price'
        ];

        $jad = new Jad($mapper);
        $jad->setPathPrefix('/api/jad');

        $result = json_decode($jad->jsonApiResult());

        $this->assertEquals(10, count($result->data));
        $this->assertEquals('645', $result->data[7]->id);
        $this->assertEquals('0.99', $result->data[7]->attributes->price);

    }

    public function testOffset()
    {
        $_GET = [
            'page' => [
                'offset' => 5,
                'limit' => 5
            ],

            'sort' => 'id'
        ];

        $mapper = new ArrayMapper(Manager::getInstance()->getEm());
        $mapper->add('tracks', ['entityClass' => 'Jad\Database\Entities\Tracks']);

        $jad = new Jad( $mapper);
        $jad->setPathPrefix('/api/jad');

        $result = json_decode($jad->jsonApiResult());

        $this->assertEquals('422', $result->data[0]->id);
        $this->assertEquals('603', $result->data[1]->id);
        $this->assertEquals('645', $result->data[2]->id);
        $this->assertEquals('678', $result->data[3]->id);
        $this->assertEquals('1139', $result->data[4]->id);

        $this->assertTrue(true);
    }
}
