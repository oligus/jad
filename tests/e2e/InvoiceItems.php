<?php

namespace Jad\E2E;

use Jad\Jad;
use Jad\Tests\DBTestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use PHPUnit\DbUnit\DataSet\CsvDataSet;
use Spatie\Snapshots\MatchesSnapshots;

class InvoiceItems extends DBTestCase
{
    use MatchesSnapshots;

    public function testFilter()
    {
        $_SERVER['REQUEST_URI']  = '/invoice-items';
        $_GET = ['filter' => ['invoice-items' => ['quantity' => ['gt' => '1']]]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function getDataSet()
    {
        $dataSet = new CsvDataSet();
        $dataSet->addTable('Invoice_Items', dirname(__DIR__ ) . '/fixtures//invoice_items.csv');
        return $dataSet;
    }

}
