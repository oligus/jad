<?php

namespace Jad\E2E;

use Jad\Tests\DBTestCase;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Jad;

use PHPUnit\DbUnit\DataSet\CsvDataSet;
use Spatie\Snapshots\MatchesSnapshots;

class GenresTest extends DBTestCase
{
    use MatchesSnapshots;

    public function testResourceNotFoundException()
    {
        $_SERVER['REQUEST_URI']  = '/notfound';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testFetchCollection()
    {
        $_SERVER['REQUEST_URI']  = '/genres';

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testLimit()
    {
        $_SERVER['REQUEST_URI']  = '/genres';

        $_GET = ['page' => ['offset' => 0, 'limit' => 5]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testOffset()
    {
        $_SERVER['REQUEST_URI']  = '/genres';
        $_GET = ['page' => ['page' => 10, 'size' => 5]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testSortAsc()
    {
        $_SERVER['REQUEST_URI']  = '/genres';
        $_GET = ['page' => ['number' => 0, 'size' => 10], 'sort' => 'name',];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testSortDesc()
    {
        $_SERVER['REQUEST_URI']  = '/genres';
        $_GET = ['page' => ['number' => 0, 'size' => 10], 'sort' => '-name',];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testCreate()
    {
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

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }

    public function testUpdate()
    {
        $_SERVER['REQUEST_URI']  = '/genres/16';
        $_SERVER['REQUEST_METHOD'] = 'PATCH';

        $input = new \stdClass();
        $input->data = new \stdClass();
        $input->data->type = 'genres';
        $input->data->id = '16';
        $input->data->attributes = new \stdClass();
        $input->data->attributes->name = 'Hello World!';

        $_POST = ['input' => json_encode($input)];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
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