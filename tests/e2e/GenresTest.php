<?php

namespace Jad\E2E;

use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Jad;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\CsvDataSet;

class GenresTest extends TestCase
{
    use TestCaseTrait;

    public function setUp()
    {
        parent::setUp();
        $_GET = [];
    }


    public function getConnection()
    {
        $pdo = new \PDO('sqlite://' . dirname(__DIR__ ) . '/fixtures/test_db.sqlite');
        return $this->createDefaultDBConnection($pdo, ':memory:');
    }

    public function getDataSet()
    {
        $dataSet = new CsvDataSet();
        $dataSet->addTable('genres', dirname(__DIR__ ) . '/fixtures/genres.csv');
        return $dataSet;
    }

    public function testResourceNotFoundException()
    {
        $_SERVER = ['REQUEST_URI' => '/notfound'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"errors":[{"status":404,"title":"Resource Not Found error","detail":"Resource type not found [notfound]"}]}';
        $jad->jsonApiResult();

        $this->expectOutputString($expected);
    }

    public function testFetchCollection()
    {
        $_SERVER = ['REQUEST_URI' => '/genres'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":1,"type":"genre","attributes":{"name":"Rock"}},{"id":2,"type":"genre","attributes":{"name":"Jazz"}},{"id":3,"type":"genre","attributes":{"name":"Metal"}},{"id":4,"type":"genre","attributes":{"name":"Alternative & Punk"}},{"id":5,"type":"genre","attributes":{"name":"Rock And Roll"}},{"id":6,"type":"genre","attributes":{"name":"Blues"}},{"id":7,"type":"genre","attributes":{"name":"Latin"}},{"id":8,"type":"genre","attributes":{"name":"Reggae"}},{"id":9,"type":"genre","attributes":{"name":"Pop"}},{"id":10,"type":"genre","attributes":{"name":"Soundtrack"}},{"id":11,"type":"genre","attributes":{"name":"Bossa Nova"}},{"id":12,"type":"genre","attributes":{"name":"Easy Listening"}},{"id":13,"type":"genre","attributes":{"name":"Heavy Metal"}},{"id":14,"type":"genre","attributes":{"name":"R&B\/Soul"}},{"id":15,"type":"genre","attributes":{"name":"Electronica\/Dance"}},{"id":16,"type":"genre","attributes":{"name":"World"}},{"id":17,"type":"genre","attributes":{"name":"Hip Hop\/Rap"}},{"id":18,"type":"genre","attributes":{"name":"Science Fiction"}},{"id":19,"type":"genre","attributes":{"name":"TV Shows"}},{"id":20,"type":"genre","attributes":{"name":"Sci Fi & Fantasy"}},{"id":21,"type":"genre","attributes":{"name":"Drama"}},{"id":22,"type":"genre","attributes":{"name":"Comedy"}},{"id":23,"type":"genre","attributes":{"name":"Alternative"}},{"id":24,"type":"genre","attributes":{"name":"Classical"}},{"id":25,"type":"genre","attributes":{"name":"Opera"}}],"links":{"self":"http:\/\/:\/genres"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testLimit()
    {
        $_SERVER = ['REQUEST_URI' => '/genres'];

        $_GET = ['page' => ['offset' => 0, 'limit' => 5]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":1,"type":"genre","attributes":{"name":"Rock"}},{"id":2,"type":"genre","attributes":{"name":"Jazz"}},{"id":3,"type":"genre","attributes":{"name":"Metal"}},{"id":4,"type":"genre","attributes":{"name":"Alternative & Punk"}},{"id":5,"type":"genre","attributes":{"name":"Rock And Roll"}}],"links":{"self":"http:\/\/:\/genres"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testOffset()
    {
        $_SERVER = ['REQUEST_URI' => '/genres'];
        $_GET = ['page' => ['offset' => 10, 'limit' => 5]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":11,"type":"genre","attributes":{"name":"Bossa Nova"}},{"id":12,"type":"genre","attributes":{"name":"Easy Listening"}},{"id":13,"type":"genre","attributes":{"name":"Heavy Metal"}},{"id":14,"type":"genre","attributes":{"name":"R&B\/Soul"}},{"id":15,"type":"genre","attributes":{"name":"Electronica\/Dance"}}],"links":{"self":"http:\/\/:\/genres"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testSortAsc()
    {
        $_SERVER = ['REQUEST_URI' => '/genres'];
        $_GET = ['page' => ['offset' => 0, 'limit' => 10], 'sort' => 'name',];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":23,"type":"genre","attributes":{"name":"Alternative"}},{"id":4,"type":"genre","attributes":{"name":"Alternative & Punk"}},{"id":6,"type":"genre","attributes":{"name":"Blues"}},{"id":11,"type":"genre","attributes":{"name":"Bossa Nova"}},{"id":24,"type":"genre","attributes":{"name":"Classical"}},{"id":22,"type":"genre","attributes":{"name":"Comedy"}},{"id":21,"type":"genre","attributes":{"name":"Drama"}},{"id":12,"type":"genre","attributes":{"name":"Easy Listening"}},{"id":15,"type":"genre","attributes":{"name":"Electronica\/Dance"}},{"id":13,"type":"genre","attributes":{"name":"Heavy Metal"}}],"links":{"self":"http:\/\/:\/genres"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testSortDesc()
    {
        $_SERVER = ['REQUEST_URI' => '/genres'];
        $_GET = ['page' => ['offset' => 0, 'limit' => 10], 'sort' => '-name',];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":16,"type":"genre","attributes":{"name":"World"}},{"id":19,"type":"genre","attributes":{"name":"TV Shows"}},{"id":10,"type":"genre","attributes":{"name":"Soundtrack"}},{"id":18,"type":"genre","attributes":{"name":"Science Fiction"}},{"id":20,"type":"genre","attributes":{"name":"Sci Fi & Fantasy"}},{"id":5,"type":"genre","attributes":{"name":"Rock And Roll"}},{"id":1,"type":"genre","attributes":{"name":"Rock"}},{"id":8,"type":"genre","attributes":{"name":"Reggae"}},{"id":14,"type":"genre","attributes":{"name":"R&B\/Soul"}},{"id":9,"type":"genre","attributes":{"name":"Pop"}}],"links":{"self":"http:\/\/:\/genres"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function xtestFetchCollectionItemsNoRelation()
    {
        $_SERVER = ['REQUEST_URI' => '/genres'];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $result = json_decode($jad->jsonApiResult());
        $this->assertEquals(26, count($result->data));

        $item = $result->data[0];
        $this->assertEquals(15, $item->id);
        $this->assertEquals('Go Down', $item->attributes->name);
        $this->assertEquals('AC/DC', $item->attributes->composer);

        $_GET = [
            'page' => [
                'offset' => 0,
                'limit' => 5
            ],

            'sort' => '-name',
            'fields' => [
                'tracks' => 'composer'
            ]

        ];

        $jad = new Jad($mapper);

        $result = json_decode($jad->jsonApiResult());
        $this->assertEquals(5, count($result->data));

        $item = $result->data[0];
        $this->assertEquals(2957, $item->id);
        $this->assertEquals('U2', $item->attributes->composer);
        $this->assertFalse(property_exists($item->attributes, 'name'));
    }

}