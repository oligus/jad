<?php

namespace Jad\E2E;

use Jad\Tests\TestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Jad;
use Jad\Configure;

use PHPUnit\DbUnit\TestCaseTrait;
use PHPUnit\DbUnit\DataSet\CsvDataSet;

class InvoiceItems extends TestCase
{
    use TestCaseTrait;

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
        $dataSet->addTable('Invoice_Items', dirname(__DIR__ ) . '/fixtures//invoice_items.csv');
        return $dataSet;
    }

    public function testFilter()
    {
        $_SERVER['REQUEST_URI']  = '/invoice-items';
        $_GET = ['filter' => ['invoice-items' => ['quantity' => ['gt' => '1']]]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        $expected = '{"data":[{"id":"85","type":"invoice-items","attributes":{"invoice-id":17,"track-id":488,"unit-price":"0.99","quantity":3},"relationships":{"invoices":{"links":{"self":"http:\/\/:\/invoice-items\/85\/relationship\/invoices","related":"http:\/\/:\/invoice-items\/85\/invoices"}}}},{"id":"95","type":"invoice-items","attributes":{"invoice-id":18,"track-id":542,"unit-price":"0.99","quantity":4},"relationships":{"invoices":{"links":{"self":"http:\/\/:\/invoice-items\/95\/relationship\/invoices","related":"http:\/\/:\/invoice-items\/95\/invoices"}}}},{"id":"110","type":"invoice-items","attributes":{"invoice-id":19,"track-id":671,"unit-price":"0.99","quantity":5},"relationships":{"invoices":{"links":{"self":"http:\/\/:\/invoice-items\/110\/relationship\/invoices","related":"http:\/\/:\/invoice-items\/110\/invoices"}}}}],"links":{"self":"http:\/\/:\/invoice-items"}}';

        $jad->jsonApiResult();
        $this->expectOutputString($expected);
    }
}
