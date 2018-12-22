<?php

namespace Jad\E2E;

use Jad\Jad;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

/**
 * Class PaginationTest
 * @package Jad\E2E
 */
class PaginationTest extends TestCase
{
    use MatchesSnapshots;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
    public function testSize()
    {
        $_SERVER['REQUEST_URI'] = '/genres';
        $_GET = ['page' => ['page' => 1, 'size' => 5]];

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
    public function testNumber()
    {
        $_SERVER['REQUEST_URI'] = '/genres';
        $_GET = ['page' => ['number' => 2, 'size' => 5]];

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
    public function testLast()
    {
        $_SERVER['REQUEST_URI'] = '/genres';
        $_GET = ['page' => ['number' => 9, 'size' => 3]];

        $mapper = new AnnotationsMapper(Manager::getInstance()->getEm());
        $jad = new Jad($mapper);

        ob_start();
        $jad->jsonApiResult();
        $output = ob_get_clean();

        $this->assertMatchesJsonSnapshot($output);
    }
}
