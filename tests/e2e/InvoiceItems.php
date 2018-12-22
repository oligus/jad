<?php

namespace Jad\E2E;

use Jad\Jad;
use Jad\Database\Manager;
use Jad\Map\AnnotationsMapper;
use Jad\Tests\TestCase;
use Spatie\Snapshots\MatchesSnapshots;

/**
 * Class InvoiceItems
 * @package Jad\E2E
 */
class InvoiceItems extends TestCase
{
    use MatchesSnapshots;

    /**
     * @throws \Doctrine\Common\Annotations\AnnotationException
     */
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
}
