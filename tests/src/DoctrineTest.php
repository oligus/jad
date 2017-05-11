<?php

use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Jad;
use Jad\Map\EntityMap;

class DoctrineTest extends TestCase
{
    public function testSort()
    {
        $entityMap = new EntityMap([
            'tracks' => 'Jad\Database\Entities\Tracks'
        ]);

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

        $jad = new Jad(Manager::getInstance()->getEm(), $entityMap);
        $jad->setPathPrefix('/api/jad');

        $result = json_decode($jad->jsonApiResult());

        $this->assertEquals(5, count($result->data));
        $this->assertEquals('2078', $result->data[2]->id);
        $this->assertEquals('Óculos', $result->data[2]->attributes->name);

        $_GET = [
            'page' => [
                'offset' => 0,
                'limit' => 10
            ],

            'sort' => 'price'
        ];

        $jad = new Jad(Manager::getInstance()->getEm(), $entityMap);
        $jad->setPathPrefix('/api/jad');

        $result = json_decode($jad->jsonApiResult());

        $this->assertEquals(10, count($result->data));
        $this->assertEquals('8', $result->data[7]->id);
        $this->assertEquals('0.99', $result->data[7]->attributes->price);

    }

    public function testOffset()
    {
        $entityMap = new EntityMap([
            'tracks' => 'Jad\Database\Entities\Tracks'
        ]);

        $_GET = [
            'page' => [
                'offset' => 5,
                'limit' => 5
            ],

            'sort' => 'id'
        ];

        $jad = new Jad(Manager::getInstance()->getEm(), $entityMap);
        $jad->setPathPrefix('/api/jad');

        $result = json_decode($jad->jsonApiResult());

        $this->assertEquals('6', $result->data[0]->id);
        $this->assertEquals('7', $result->data[1]->id);
        $this->assertEquals('8', $result->data[2]->id);
        $this->assertEquals('9', $result->data[3]->id);
        $this->assertEquals('10', $result->data[4]->id);

        $this->assertTrue(true);
    }
}
