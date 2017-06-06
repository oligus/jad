<?php

namespace Jad\E2E;

use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Jad;
use Jad\Configure;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\CsvDataSet;

class GenresTest extends TestCase
{
    use TestCaseTrait;

    /**
     * delete from your_table;
    delete from sqlite_sequence where name='your_table';
     */
    public function setUp()
    {
        parent::setUp();
        $this->databaseTester = null;

        $this->getDatabaseTester()->setSetUpOperation($this->getSetUpOperation());
        $this->getDatabaseTester()->setDataSet($this->getDataSet());
        $this->getDatabaseTester()->onSetUp();

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

    public function testCreate()
    {
        Configure::getInstance()->setConfig('testMode', true);

        $_SERVER = ['REQUEST_URI' => '/genres'];
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'genre';
        $input->data->attributes = new \stdClass();
        $input->data->attributes->name = 'Created Genre';

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":{"id":26,"type":"genre","attributes":{"name":"Created Genre"}},"links":{"self":"http:\/\/:\/genres"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testUpdate()
    {
        Configure::getInstance()->setConfig('testMode', true);

        $_SERVER = ['REQUEST_URI' => '/genres/26'];
        $_SERVER['REQUEST_METHOD'] = 'PATCH';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'genre';
        $input->data->id = '26';
        $input->data->attributes = new \stdClass();
        $input->data->attributes->name = 'Updated Genre';

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":{"id":26,"type":"genre","attributes":{"name":"Updated Genre"}},"links":{"self":"http:\/\/:\/genres\/26"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testDelete()
    {
        $_SERVER = ['REQUEST_URI' => '/genres/26'];
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);
        $jad->jsonApiResult();
        $this->assertTrue(true);
    }

    public function testDeleteVerify()
    {
        $_SERVER = ['REQUEST_URI' => '/genres/26'];
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"errors":[{"status":404,"title":"Resource Not Found error","detail":"Resource of type [genre] with id [26] could not be found."}]}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }
}