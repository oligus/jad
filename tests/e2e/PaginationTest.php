<?php

namespace Jad\E2E;

use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Jad;
use Jad\Configure;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\CsvDataSet;

class PaginationTest extends TestCase
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

    public function testSize()
    {
        $_SERVER = ['REQUEST_URI' => '/genres'];
        $_GET = ['page' => ['page' => 1, 'size' => 5]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":1,"type":"genres","attributes":{"name":"Rock"}},{"id":2,"type":"genres","attributes":{"name":"Jazz"}},{"id":3,"type":"genres","attributes":{"name":"Metal"}},{"id":4,"type":"genres","attributes":{"name":"Alternative & Punk"}},{"id":5,"type":"genres","attributes":{"name":"Rock And Roll"}}],"links":{"self":"http:\/\/:\/genres?page[size]=5&page[number]=1","first":"http:\/\/:\/genres?page[size]=5&page[number]=1","last":"http:\/\/:\/genres?page[size]=5&page[number]=5","next":"http:\/\/:\/genres?page[size]=5&page[number]=2"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testNumber()
    {
        $_SERVER = ['REQUEST_URI' => '/genres'];
        $_GET = ['page' => ['number' => 2, 'size' => 5]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":6,"type":"genres","attributes":{"name":"Blues"}},{"id":7,"type":"genres","attributes":{"name":"Latin"}},{"id":8,"type":"genres","attributes":{"name":"Reggae"}},{"id":9,"type":"genres","attributes":{"name":"Pop"}},{"id":10,"type":"genres","attributes":{"name":"Soundtrack"}}],"links":{"self":"http:\/\/:\/genres?page[size]=5&page[number]=2","first":"http:\/\/:\/genres?page[size]=5&page[number]=1","last":"http:\/\/:\/genres?page[size]=5&page[number]=5","next":"http:\/\/:\/genres?page[size]=5&page[number]=3","previous":"http:\/\/:\/genres?page[size]=5&page[number]=1"}}';
        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }

    public function testLast()
    {
        $_SERVER = ['REQUEST_URI' => '/genres'];
        $_GET = ['page' => ['number' => 9, 'size' => 3]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":25,"type":"genres","attributes":{"name":"Opera"}}],"links":{"self":"http:\/\/:\/genres?page[size]=3&page[number]=9","first":"http:\/\/:\/genres?page[size]=3&page[number]=1","last":"http:\/\/:\/genres?page[size]=3&page[number]=9","previous":"http:\/\/:\/genres?page[size]=3&page[number]=8"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }
}
