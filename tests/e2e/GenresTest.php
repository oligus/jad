<?php

namespace Jad\E2E;

use Jad\Jad;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

/**
 * Class GenresTest
 * @package Jad\E2E
 */
class GenresTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
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

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
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

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
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

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
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

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
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

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
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

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
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

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
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

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
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
     * @throws \Doctrine\Common\Annotations\AnnotationException
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
}
