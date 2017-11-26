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
        Configure::getInstance()->setConfig('strict', true);
        $_SERVER['REQUEST_URI']  = '/notfound';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"errors":[{"status":"404","title":"Resource Not Found error","detail":"Resource type not found [notfound]"}]}';
        $jad->jsonApiResult();

        $this->expectOutputString($expected);
    }

    public function testFetchCollection()
    {
        $_SERVER['REQUEST_URI']  = '/genres';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"1","type":"genres","attributes":{"name":"Rock"}},{"id":"2","type":"genres","attributes":{"name":"Jazz"}},{"id":"3","type":"genres","attributes":{"name":"Metal"}},{"id":"4","type":"genres","attributes":{"name":"Alternative & Punk"}},{"id":"5","type":"genres","attributes":{"name":"Rock And Roll"}},{"id":"6","type":"genres","attributes":{"name":"Blues"}},{"id":"7","type":"genres","attributes":{"name":"Latin"}},{"id":"8","type":"genres","attributes":{"name":"Reggae"}},{"id":"9","type":"genres","attributes":{"name":"Pop"}},{"id":"10","type":"genres","attributes":{"name":"Soundtrack"}},{"id":"11","type":"genres","attributes":{"name":"Bossa Nova"}},{"id":"12","type":"genres","attributes":{"name":"Easy Listening"}},{"id":"13","type":"genres","attributes":{"name":"Heavy Metal"}},{"id":"14","type":"genres","attributes":{"name":"R&B\/Soul"}},{"id":"15","type":"genres","attributes":{"name":"Electronica\/Dance"}},{"id":"16","type":"genres","attributes":{"name":"World"}},{"id":"17","type":"genres","attributes":{"name":"Hip Hop\/Rap"}},{"id":"18","type":"genres","attributes":{"name":"Science Fiction"}},{"id":"19","type":"genres","attributes":{"name":"TV Shows"}},{"id":"20","type":"genres","attributes":{"name":"Sci Fi & Fantasy"}},{"id":"21","type":"genres","attributes":{"name":"Drama"}},{"id":"22","type":"genres","attributes":{"name":"Comedy"}},{"id":"23","type":"genres","attributes":{"name":"Alternative"}},{"id":"24","type":"genres","attributes":{"name":"Classical"}},{"id":"25","type":"genres","attributes":{"name":"Opera"}}],"links":{"self":"http:\/\/:\/genres?page[size]=25&page[number]=1","first":"http:\/\/:\/genres?page[size]=25&page[number]=1","last":"http:\/\/:\/genres?page[size]=25&page[number]=1"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testLimit()
    {
        $_SERVER['REQUEST_URI']  = '/genres';

        $_GET = ['page' => ['offset' => 0, 'limit' => 5]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"1","type":"genres","attributes":{"name":"Rock"}},{"id":"2","type":"genres","attributes":{"name":"Jazz"}},{"id":"3","type":"genres","attributes":{"name":"Metal"}},{"id":"4","type":"genres","attributes":{"name":"Alternative & Punk"}},{"id":"5","type":"genres","attributes":{"name":"Rock And Roll"}},{"id":"6","type":"genres","attributes":{"name":"Blues"}},{"id":"7","type":"genres","attributes":{"name":"Latin"}},{"id":"8","type":"genres","attributes":{"name":"Reggae"}},{"id":"9","type":"genres","attributes":{"name":"Pop"}},{"id":"10","type":"genres","attributes":{"name":"Soundtrack"}},{"id":"11","type":"genres","attributes":{"name":"Bossa Nova"}},{"id":"12","type":"genres","attributes":{"name":"Easy Listening"}},{"id":"13","type":"genres","attributes":{"name":"Heavy Metal"}},{"id":"14","type":"genres","attributes":{"name":"R&B\/Soul"}},{"id":"15","type":"genres","attributes":{"name":"Electronica\/Dance"}},{"id":"16","type":"genres","attributes":{"name":"World"}},{"id":"17","type":"genres","attributes":{"name":"Hip Hop\/Rap"}},{"id":"18","type":"genres","attributes":{"name":"Science Fiction"}},{"id":"19","type":"genres","attributes":{"name":"TV Shows"}},{"id":"20","type":"genres","attributes":{"name":"Sci Fi & Fantasy"}},{"id":"21","type":"genres","attributes":{"name":"Drama"}},{"id":"22","type":"genres","attributes":{"name":"Comedy"}},{"id":"23","type":"genres","attributes":{"name":"Alternative"}},{"id":"24","type":"genres","attributes":{"name":"Classical"}},{"id":"25","type":"genres","attributes":{"name":"Opera"}}],"links":{"self":"http:\/\/:\/genres?page[size]=25&page[number]=1","first":"http:\/\/:\/genres?page[size]=25&page[number]=1","last":"http:\/\/:\/genres?page[size]=25&page[number]=1"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testOffset()
    {
        $_SERVER['REQUEST_URI']  = '/genres';
        $_GET = ['page' => ['page' => 10, 'size' => 5]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"1","type":"genres","attributes":{"name":"Rock"}},{"id":"2","type":"genres","attributes":{"name":"Jazz"}},{"id":"3","type":"genres","attributes":{"name":"Metal"}},{"id":"4","type":"genres","attributes":{"name":"Alternative & Punk"}},{"id":"5","type":"genres","attributes":{"name":"Rock And Roll"}}],"links":{"self":"http:\/\/:\/genres?page[size]=5&page[number]=1","first":"http:\/\/:\/genres?page[size]=5&page[number]=1","last":"http:\/\/:\/genres?page[size]=5&page[number]=5","next":"http:\/\/:\/genres?page[size]=5&page[number]=2"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testSortAsc()
    {
        $_SERVER['REQUEST_URI']  = '/genres';
        $_GET = ['page' => ['number' => 0, 'size' => 10], 'sort' => 'name',];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"23","type":"genres","attributes":{"name":"Alternative"}},{"id":"4","type":"genres","attributes":{"name":"Alternative & Punk"}},{"id":"6","type":"genres","attributes":{"name":"Blues"}},{"id":"11","type":"genres","attributes":{"name":"Bossa Nova"}},{"id":"24","type":"genres","attributes":{"name":"Classical"}},{"id":"22","type":"genres","attributes":{"name":"Comedy"}},{"id":"21","type":"genres","attributes":{"name":"Drama"}},{"id":"12","type":"genres","attributes":{"name":"Easy Listening"}},{"id":"15","type":"genres","attributes":{"name":"Electronica\/Dance"}},{"id":"13","type":"genres","attributes":{"name":"Heavy Metal"}}],"links":{"self":"http:\/\/:\/genres?page[size]=10&page[number]=1","first":"http:\/\/:\/genres?page[size]=10&page[number]=1","last":"http:\/\/:\/genres?page[size]=10&page[number]=3","next":"http:\/\/:\/genres?page[size]=10&page[number]=2"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testSortDesc()
    {
        $_SERVER['REQUEST_URI']  = '/genres';
        $_GET = ['page' => ['number' => 0, 'size' => 10], 'sort' => '-name',];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"16","type":"genres","attributes":{"name":"World"}},{"id":"19","type":"genres","attributes":{"name":"TV Shows"}},{"id":"10","type":"genres","attributes":{"name":"Soundtrack"}},{"id":"18","type":"genres","attributes":{"name":"Science Fiction"}},{"id":"20","type":"genres","attributes":{"name":"Sci Fi & Fantasy"}},{"id":"5","type":"genres","attributes":{"name":"Rock And Roll"}},{"id":"1","type":"genres","attributes":{"name":"Rock"}},{"id":"8","type":"genres","attributes":{"name":"Reggae"}},{"id":"14","type":"genres","attributes":{"name":"R&B\/Soul"}},{"id":"9","type":"genres","attributes":{"name":"Pop"}}],"links":{"self":"http:\/\/:\/genres?page[size]=10&page[number]=1","first":"http:\/\/:\/genres?page[size]=10&page[number]=1","last":"http:\/\/:\/genres?page[size]=10&page[number]=3","next":"http:\/\/:\/genres?page[size]=10&page[number]=2"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testCreate()
    {
        Configure::getInstance()->setConfig('testMode', true);

        $_SERVER['REQUEST_URI']  = '/genres';
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'genres';
        $input->data->attributes = new \stdClass();
        $input->data->attributes->name = 'Created Genre';

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":{"id":"26","type":"genres","attributes":{"name":"Created Genre"}},"links":{"self":"http:\/\/:\/genres"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testUpdate()
    {
        Configure::getInstance()->setConfig('testMode', true);

        $_SERVER['REQUEST_URI']  = '/genres/26';
        $_SERVER['REQUEST_METHOD'] = 'PATCH';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'genres';
        $input->data->id = '26';
        $input->data->attributes = new \stdClass();
        $input->data->attributes->name = 'Updated Genre';

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":{"id":"26","type":"genres","attributes":{"name":"Updated Genre"}},"links":{"self":"http:\/\/:\/genres\/26"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testDelete()
    {
        $_SERVER['REQUEST_URI']  = '/genres/26';
        $_SERVER['REQUEST_METHOD'] = 'DELETE';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);
        $jad->jsonApiResult();
        $this->assertTrue(true);
    }

    /**
     * @depends testDelete
     */
    public function testDeleteVerify()
    {
        $_SERVER['REQUEST_URI']  = '/genres/26';
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"errors":[{"status":"404","title":"Resource Not Found error","detail":"Resource of type [genres] with id [26] could not be found."}]}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }
}