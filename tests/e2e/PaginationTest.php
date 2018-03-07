<?php

namespace Jad\E2E;

use Jad\Jad;
use Jad\Tests\DBTestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use PHPUnit\DbUnit\DataSet\CsvDataSet;
use Spatie\Snapshots\MatchesSnapshots;

class PaginationTest extends DBTestCase
{
    use MatchesSnapshots;

    public function testSize()
    {
        $_SERVER['REQUEST_URI']  = '/genres';
        $_GET = ['page' => ['page' => 1, 'size' => 5]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testNumber()
    {
        $_SERVER['REQUEST_URI']  = '/genres';
        $_GET = ['page' => ['number' => 2, 'size' => 5]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testLast()
    {
        $_SERVER['REQUEST_URI']  = '/genres';
        $_GET = ['page' => ['number' => 9, 'size' => 3]];

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
        $dataSet->addTable('genres', dirname(__DIR__ ) . '/fixtures/genres.csv');
        return $dataSet;
    }
}
